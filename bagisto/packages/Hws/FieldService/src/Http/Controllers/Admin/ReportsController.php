<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\Task;
use Hws\FieldService\Models\Attendance;
use Hws\FieldService\Models\ExpenseClaim;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // 1. Task completions by type
        $tasksByType = Task::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        // 2. Task counts by step (status)
        $tasksByStep = Task::select('step', DB::raw('count(*) as total'))
            ->groupBy('step')
            ->get();

        // 3. Sales totals
        $totalSales = Task::where('step', 4)->sum('sale_amount');

        // 4. Approved expenses totals
        $approvedExpenses = ExpenseClaim::where('status', 'approved')->sum('amount');
        $pendingExpenses = ExpenseClaim::where('status', 'pending')->sum('amount');

        // 5. Attendance records count
        $totalAttendanceLogs = Attendance::count();

        return view('hws::admin.reports.index', compact(
            'tasksByType',
            'tasksByStep',
            'totalSales',
            'approvedExpenses',
            'pendingExpenses',
            'totalAttendanceLogs'
        ));
    }
}
