<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\Task;
use Webkul\User\Models\Admin;
use Illuminate\Http\Request;

class ServiceRequestsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return app(\Hws\FieldService\DataGrids\TaskDataGrid::class)->toJson();
        }

        $employees = Admin::all();

        return view('hws::admin.service-requests.index', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'priority' => 'required|in:urgent,high,normal,low',
            'type' => 'required|in:installation,amc_service,complaint,service',
            'assigned_to' => 'nullable|exists:admins,id',
        ]);

        Task::create([
            'task_no' => 'SR-' . strtoupper(bin2hex(random_bytes(3))),
            'type' => $request->type,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'priority' => $request->priority,
            'assigned_to' => $request->assigned_to,
            'step' => 0,
        ]);

        session()->flash('success', 'Service Request created successfully.');

        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:urgent,high,normal,low',
            'step' => 'required|integer|between:0,4',
            'assigned_to' => 'nullable|exists:admins,id',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->only(['priority', 'step', 'assigned_to']));

        session()->flash('success', 'Service Request updated successfully.');

        return redirect()->back();
    }
}
