<?php

namespace Hws\FieldService\Listeners;

use Hws\FieldService\Models\Notification;
use Hws\FieldService\Models\SiteSurvey;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;
use Webkul\User\Models\Admin;

class CreateLeadFromOrder
{
    public function handle($order): void
    {
        try {
            if (SiteSurvey::where('order_id', $order->id)->exists()) {
                return;
            }

            $customer = $this->resolveCustomer($order);

            if ($customer && ! $order->customer_id) {
                $order->customer_id = $customer->id;
                $order->customer_type = Customer::class;
                $order->is_guest = 0;
                $order->saveQuietly();
            }

            $address = $order->shipping_address ?: $order->billing_address;
            $reference = 'ORD-' . $order->increment_id;
            $lead = SiteSurvey::create([
                'customer_id'      => $customer?->id,
                'order_id'         => $order->id,
                'customer_name'    => trim($order->customer_first_name . ' ' . $order->customer_last_name),
                'customer_phone'   => $address?->phone ?: $customer?->phone,
                'customer_email'   => $order->customer_email,
                'customer_address' => $this->formatAddress($address),
                'property_type'    => 'other',
                'status'           => 'won',
                'temperature'      => 'hot',
                'source'           => 'Website Checkout',
                'request_type'     => 'checkout',
                'reference_no'     => $reference,
                'request_details'  => [
                    'order_id'     => $order->id,
                    'order_number' => $order->increment_id,
                    'order_status' => $order->status,
                    'grand_total'  => (float) $order->grand_total,
                    'currency'     => $order->order_currency_code,
                    'items'        => $order->items->map(fn ($item) => [
                        'name'     => $item->name,
                        'sku'      => $item->sku,
                        'quantity' => (float) $item->qty_ordered,
                        'price'    => (float) $item->price,
                    ])->values()->all(),
                ],
                'notes'            => "Website order #{$order->increment_id} placed for {$order->order_currency_code} {$order->grand_total}.",
            ]);

            if (Schema::hasTable('hws_notifications')) {
                Admin::query()->pluck('id')->each(fn ($adminId) => Notification::create([
                    'admin_id' => $adminId,
                    'title'     => 'New Checkout Lead',
                    'message'   => "{$reference}: {$lead->customer_name} placed an order.",
                    'is_read'   => false,
                ]));
            }
        } catch (\Throwable $exception) {
            // CRM sync must never block the customer's order.
            Log::error('HWS checkout CRM sync failed', [
                'order_id' => $order->id ?? null,
                'error'    => $exception->getMessage(),
            ]);
        }
    }

    private function resolveCustomer($order): ?Customer
    {
        if (! $order->customer_email) {
            return null;
        }

        $customer = Customer::where('email', $order->customer_email)->first();

        if ($customer) {
            return $customer;
        }

        $customer = Customer::create([
            'first_name'        => $order->customer_first_name ?: 'Customer',
            'last_name'         => $order->customer_last_name ?: 'Account',
            'email'             => $order->customer_email,
            'phone'             => ($order->shipping_address ?: $order->billing_address)?->phone,
            'password'          => Hash::make(Str::random(48)),
            'customer_group_id' => 2,
            'status'            => 1,
            'is_verified'       => 1,
            'notes'             => 'Automatically created from website checkout. Password setup required.',
        ]);

        try {
            Password::broker('customers')->sendResetLink(['email' => $customer->email]);
        } catch (\Throwable $exception) {
            Log::warning('Customer activation email could not be sent', [
                'customer_id' => $customer->id,
                'error'       => $exception->getMessage(),
            ]);
        }

        return $customer;
    }

    private function formatAddress($address): string
    {
        if (! $address) {
            return 'Address captured with website order';
        }

        return collect([
            $address->address1,
            $address->city,
            $address->state,
            $address->postcode,
            $address->country,
        ])->flatten()->filter()->implode(', ');
    }
}
