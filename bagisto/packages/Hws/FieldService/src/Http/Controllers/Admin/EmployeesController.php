<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Webkul\User\Models\Admin;
use Hws\FieldService\Models\Task;
use Hws\FieldService\Models\Attendance;
use Illuminate\Support\Carbon;

class EmployeesController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $employees = Admin::with('role')->get()->map(function ($employee) use ($today) {
            // Check if they checked in today
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();

            // Check their active task
            $activeTask = Task::where('assigned_to', $employee->id)
                ->where('step', '>', 0)
                ->where('step', '<', 4)
                ->first();

            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'role' => $employee->role ? $employee->role->name : 'N/A',
                'status' => $employee->status ? 'Active' : 'Inactive',
                'checked_in' => $attendance && !$attendance->check_out_time,
                'check_in' => $attendance ? $attendance->check_in_time : null,
                'active_task' => $activeTask ? $activeTask->task_no . ' (' . ucfirst($activeTask->type) . ')' : 'None',
            ];
        });

        return view('hws::admin.employees.index', compact('employees'));
    }
}
