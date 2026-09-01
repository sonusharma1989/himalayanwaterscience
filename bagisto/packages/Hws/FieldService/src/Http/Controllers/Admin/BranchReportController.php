<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Hws\FieldService\Models\Branch;

class BranchReportController extends Controller
{
    public function index(Request $request)
    {
        if (!\Hws\FieldService\Helpers\BranchScopeHelper::isHeadOfficeUser()) {
            abort(403, 'Unauthorized access: Branch comparison reports are accessible to Head Office personnel only.');
        }

        $dbPrefix = DB::getTablePrefix();

        $branches = Branch::with('employees')->get()->map(function ($branch) use ($dbPrefix) {
            $branchId = $branch->id;

            $totalOrders = DB::table('orders')->where('branch_id', $branchId)->count();
            $totalRevenue = (float) DB::table('orders')->where('branch_id', $branchId)->sum('base_grand_total');
            $totalPaid = (float) DB::table('order_transactions')
                ->join('orders', 'orders.id', '=', 'order_transactions.order_id')
                ->where('orders.branch_id', $branchId)
                ->sum(DB::raw("CASE WHEN order_transactions.amount > 0 THEN order_transactions.amount ELSE CAST(JSON_UNQUOTE(JSON_EXTRACT(order_transactions.data, '$.amount')) AS DECIMAL(12,2)) END"));

            $totalDue = max(0, $totalRevenue - $totalPaid);

            $totalLeads = DB::table('hws_site_surveys')->where('branch_id', $branchId)->count();
            $wonLeads = DB::table('hws_site_surveys')->where('branch_id', $branchId)->where('status', 'won')->count();

            $totalServiceReqs = DB::table('hws_tasks')->where('branch_id', $branchId)->whereIn('type', ['service', 'complaint', 'amc_service'])->count();
            $openServiceReqs = DB::table('hws_tasks')->where('branch_id', $branchId)->whereIn('type', ['service', 'complaint', 'amc_service'])->where('step', '<', 4)->count();

            $totalTasks = DB::table('hws_tasks')->where('branch_id', $branchId)->count();
            $completedTasks = DB::table('hws_tasks')->where('branch_id', $branchId)->where('step', 4)->count();

            return (object) [
                'id'                 => $branch->id,
                'name'               => $branch->name,
                'code'               => $branch->code,
                'city'               => $branch->city,
                'is_head_office'     => $branch->is_head_office,
                'staff_count'        => $branch->employees->count(),
                'total_orders'       => $totalOrders,
                'total_revenue'      => $totalRevenue,
                'total_paid'         => $totalPaid,
                'total_due'          => $totalDue,
                'total_leads'        => $totalLeads,
                'lead_conv_rate'     => $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0,
                'open_service_reqs'  => $openServiceReqs,
                'total_tasks'        => $totalTasks,
                'task_comp_rate'     => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            ];
        });

        $totals = (object) [
            'total_branches'  => $branches->count(),
            'total_staff'     => $branches->sum('staff_count'),
            'total_revenue'   => $branches->sum('total_revenue'),
            'total_paid'      => $branches->sum('total_paid'),
            'total_due'       => $branches->sum('total_due'),
            'total_orders'    => $branches->sum('total_orders'),
            'open_services'   => $branches->sum('open_service_reqs'),
            'total_tasks'     => $branches->sum('total_tasks'),
        ];

        return view('hws::admin.branches.reports', compact('branches', 'totals'));
    }
}
