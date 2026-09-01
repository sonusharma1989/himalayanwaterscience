<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function dashboard()
    {
        $leadsQuery = DB::table('hws_site_surveys')->where('sales_type', 'projects');
        $ordersQuery = DB::table('orders')->where('sales_type', 'projects');
        $revenueQuery = DB::table('orders')->where('sales_type', 'projects');
        $shipmentsQuery = DB::table('shipments')->join('orders', 'shipments.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects');
        $invoicesQuery = DB::table('invoices')->join('orders', 'invoices.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects');
        $refundsQuery = DB::table('refunds')->join('orders', 'refunds.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects');
        $transQuery = DB::table('order_transactions')->join('orders', 'order_transactions.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects');

        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($leadsQuery, 'hws_site_surveys');
        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($ordersQuery, 'orders');
        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($revenueQuery, 'orders');
        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($shipmentsQuery, 'orders');
        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($invoicesQuery, 'orders');
        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($refundsQuery, 'orders');
        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($transQuery, 'orders');

        $stats = [
            'leads'        => $leadsQuery->count(),
            'orders'       => $ordersQuery->count(),
            'revenue'      => $revenueQuery->sum('grand_total'),
            'shipments'    => $shipmentsQuery->count(),
            'invoices'     => $invoicesQuery->count(),
            'refunds'      => $refundsQuery->count(),
            'transactions' => $transQuery->count(),
        ];

        return view('hws::admin.projects.dashboard', compact('stats'));
    }
}
