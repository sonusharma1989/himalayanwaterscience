<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProjectsController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'leads'        => DB::table('hws_site_surveys')->where('sales_type', 'projects')->count(),
            'orders'       => DB::table('orders')->where('sales_type', 'projects')->count(),
            'revenue'      => DB::table('orders')->where('sales_type', 'projects')->sum('grand_total'),
            'shipments'    => DB::table('shipments')->join('orders', 'shipments.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects')->count(),
            'invoices'     => DB::table('invoices')->join('orders', 'invoices.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects')->count(),
            'refunds'      => DB::table('refunds')->join('orders', 'refunds.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects')->count(),
            'transactions' => DB::table('order_transactions')->join('orders', 'order_transactions.order_id', '=', 'orders.id')->where('orders.sales_type', 'projects')->count(),
        ];

        return view('hws::admin.projects.dashboard', compact('stats'));
    }
}
