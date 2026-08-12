<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\Task;
use Webkul\User\Models\Admin;
use Illuminate\Http\Request;

class SalesLeadsController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::whereIn('type', ['sales_visit', 'site_survey'])
            ->with(['assignee', 'survey.inquiryTypes', 'photos'])
            ->orderByDesc('created_at');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('step')) {
            $query->where('step', $request->step);
        }

        $leads = $query->get();
        $employees = Admin::all();

        return view('hws::admin.sales-leads.index', compact('leads', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'priority' => 'required|in:urgent,high,normal,low',
            'type' => 'required|in:sales_visit,site_survey',
            'assigned_to' => 'nullable|exists:admins,id',
        ]);

        Task::create([
            'task_no' => 'LD-' . strtoupper(bin2hex(random_bytes(3))),
            'type' => $request->type,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'step' => 0,
        ]);

        session()->flash('success', 'Lead created successfully.');

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:urgent,high,normal,low',
            'step' => 'required|integer|between:0,4',
            'assigned_to' => 'nullable|exists:admins,id',
            'sale_amount' => 'nullable|numeric|min:0',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->only(['priority', 'step', 'assigned_to', 'sale_amount']));

        session()->flash('success', 'Lead updated successfully.');

        return redirect()->back();
    }
}
