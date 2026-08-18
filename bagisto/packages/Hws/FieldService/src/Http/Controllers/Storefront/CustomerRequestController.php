<?php

namespace Hws\FieldService\Http\Controllers\Storefront;

use Hws\FieldService\Models\Notification;
use Hws\FieldService\Models\SiteSurvey;
use Hws\FieldService\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Webkul\User\Models\Admin;

class CustomerRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'request_type'    => 'required|in:bulk_quote,engineer_callback,site_survey,installation,service,complaint,amc_service',
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'required|string|max:30',
            'customer_email'  => 'nullable|email|max:255',
            'customer_address'=> 'nullable|string|max:1000',
            'company'         => 'nullable|string|max:255',
            'product'         => 'nullable|string|max:255',
            'quantity'        => 'nullable|string|max:100',
            'preferred_date'  => 'nullable|date',
            'notes'           => 'nullable|string|max:2000',
        ]);

        $customer = auth('customer')->user();
        $customerId = $customer?->id;
        $email = $data['customer_email'] ?? $customer?->email;
        $address = ($data['customer_address'] ?? null) ?: 'Address to be confirmed';

        return DB::transaction(function () use ($data, $customerId, $email, $address) {
            if (in_array($data['request_type'], ['bulk_quote', 'engineer_callback', 'site_survey'])) {
                $reference = $this->reference('REQ');
                $lead = SiteSurvey::create([
                    'customer_id'      => $customerId,
                    'customer_name'    => $data['customer_name'],
                    'customer_phone'   => $data['customer_phone'],
                    'customer_email'   => $email,
                    'customer_address' => $address,
                    'property_type'    => 'other',
                    'status'           => 'new',
                    'temperature'      => $data['request_type'] === 'bulk_quote' ? 'hot' : 'warm',
                    'source'           => 'Website',
                    'request_type'     => $data['request_type'],
                    'reference_no'     => $reference,
                    'request_details'  => $this->details($data),
                    'notes'            => $data['notes'] ?? null,
                ]);

                $this->notifyAdmins('New Website Lead', "{$reference}: {$data['customer_name']} submitted " . str_replace('_', ' ', $data['request_type']) . '.');

                return response()->json(['success' => true, 'reference' => $lead->reference_no, 'message' => 'Request received. Our team will contact you shortly.']);
            }

            $type = $data['request_type'] === 'amc_service' ? 'amc_service' : $data['request_type'];
            $reference = $this->reference('SR');
            $task = Task::create([
                'customer_id'      => $customerId,
                'task_no'          => $reference,
                'reference_no'     => $reference,
                'type'             => $type,
                'source'           => 'Website',
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'customer_email'   => $email,
                'customer_address' => $address,
                'priority'         => $type === 'complaint' ? 'high' : 'normal',
                'step'             => 0,
                'scheduled_at'     => $data['preferred_date'] ?? null,
                'work_description' => $this->workDescription($data),
            ]);

            $this->notifyAdmins('New Service Request', "{$reference}: {$data['customer_name']} requested " . str_replace('_', ' ', $type) . '.');

            return response()->json(['success' => true, 'reference' => $task->reference_no, 'message' => 'Service request created. You can track it from My Account.']);
        });
    }

    private function reference(string $prefix): string
    {
        return $prefix . '-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
    }

    private function details(array $data): array
    {
        return array_filter([
            'company'        => $data['company'] ?? null,
            'product'        => $data['product'] ?? null,
            'quantity'       => $data['quantity'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'notes'          => $data['notes'] ?? null,
        ]);
    }

    private function workDescription(array $data): string
    {
        return collect($this->details($data))->map(fn ($value, $key) => ucwords(str_replace('_', ' ', $key)) . ': ' . $value)->implode("\n");
    }

    private function notifyAdmins(string $title, string $message): void
    {
        if (! Schema::hasTable('hws_notifications')) {
            return;
        }

        Admin::query()->pluck('id')->each(fn ($adminId) => Notification::create([
            'admin_id' => $adminId,
            'title'     => $title,
            'message'   => $message,
            'is_read'   => false,
        ]));
    }
}
