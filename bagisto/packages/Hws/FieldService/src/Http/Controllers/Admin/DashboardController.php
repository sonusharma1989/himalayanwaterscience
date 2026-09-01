<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Hws\FieldService\Models\Attendance;
use Hws\FieldService\Models\Task;
use Webkul\User\Models\Admin;

class DashboardController extends Controller
{
    /**
     * Field Service manager dashboard.
     *
     * Note on "Employees online" / "Attendance today": these are counted
     * against Admin::count() as the total headcount, since there's no
     * existing "is a field technician" flag on the admins table to filter
     * by. If field staff are meant to be a subset of all admin users
     * (e.g. a specific role), this denominator needs a proper scope —
     * right now it'll under-report the percentage if any admins aren't
     * field technicians (office/admin-only accounts).
     */
    public function index(Request $request)
    {
        $today     = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd   = Carbon::now()->endOfMonth();

        $totalEmployees = \Hws\FieldService\Helpers\BranchScopeHelper::applyScope(Admin::query())->count();

        // ── Employees online: checked in today, not yet checked out ──
        $attendanceQuery = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->whereNull('check_out_time');
        $employeesOnline = \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($attendanceQuery, 'hws_attendance')
            ->distinct('employee_id')
            ->count('employee_id');

        // ── Pending jobs: not yet completed (step 0-3), excludes surveys ──
        $pendingQuery = Task::where('step', '<', 4)
            ->where('type', '!=', 'site_survey');
        $pendingJobs = \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($pendingQuery, 'hws_tasks')
            ->count();

        // ── Completed today ──
        $completedQuery = Task::where('step', 4)
            ->whereDate('updated_at', $today);
        $completedToday = \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($completedQuery, 'hws_tasks')
            ->count();

        // ── In-Progress jobs: currently active on field (step 1 to 3) ──
        $inProgQuery = Task::whereIn('type', ['installation', 'amc_service', 'complaint', 'service'])
            ->whereIn('step', [1, 2, 3]);
        $inProgressJobs = \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($inProgQuery, 'hws_tasks')
            ->count();

        // ── Open requests: unresolved complaints & services ──
        $openReqQuery = Task::whereIn('type', ['complaint', 'service'])
            ->where('step', '<', 4);
        $openRequests = \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($openReqQuery, 'hws_tasks')
            ->count();

        // ── AMC renewals due in the next 30 days ──
        $amcRenewalsDue = Task::where('type', 'amc_service')
            ->whereNotNull('amc_renewal_date')
            ->whereBetween('amc_renewal_date', [$today, $today->copy()->addDays(30)])
            ->count();

        // ── Jobs completed this week, grouped by day (Mon-Sun) ──
        $completedThisWeekRaw = Task::where('step', 4)
            ->whereBetween('updated_at', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(fn ($task) => $task->updated_at->format('N')); // 1 (Mon) - 7 (Sun)

        $weekChart = [];
        $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($dayLabels as $i => $label) {
            $weekChart[] = [
                'label' => $label,
                'count' => $completedThisWeekRaw->get((string) ($i + 1), collect())->count(),
            ];
        }

        // ── Attendance today: % of total employees checked in at all today ──
        $attendedToday = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in_time')
            ->distinct('employee_id')
            ->count('employee_id');
        $attendancePct = $totalEmployees > 0
            ? round(($attendedToday / $totalEmployees) * 100)
            : 0;

        // ── Live employee status: who's in, and what they're on right now ──
        $liveStatus = Attendance::whereDate('date', $today)
            ->with('employee')
            ->orderByDesc('check_in_time')
            ->limit(5)
            ->get()
            ->map(function ($record) {
                $currentTask = Task::where('assigned_to', $record->employee_id)
                    ->where('step', '>', 0)
                    ->where('step', '<', 4)
                    ->orderByDesc('updated_at')
                    ->first();

                return [
                    'employee'     => $record->employee,
                    'checked_in'   => $record->check_in_time && ! $record->check_out_time,
                    'check_in'     => $record->check_in_time,
                    'current_task' => $currentTask,
                ];
            });

        return view('hws::admin.dashboard', [
            'employeesOnline'  => $employeesOnline,
            'totalEmployees'   => $totalEmployees,
            'pendingJobs'      => $pendingJobs,
            'inProgressJobs'   => $inProgressJobs,
            'completedToday'   => $completedToday,
            'openRequests'     => $openRequests,
            'amcRenewalsDue'   => $amcRenewalsDue,
            'weekChart'        => $weekChart,
            'attendancePct'    => $attendancePct,
            'attendedToday'    => $attendedToday,
            'liveStatus'       => $liveStatus,
        ]);
    }
}
