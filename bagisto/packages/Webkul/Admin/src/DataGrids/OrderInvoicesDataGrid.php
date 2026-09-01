<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class OrderInvoicesDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $index = 'id';

    /**
     * Sort order.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $dbPrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('invoices')
            ->leftJoin('orders as ors', 'invoices.order_id', '=', 'ors.id')
            ->select(
                'invoices.id as id',
                'ors.increment_id as order_id',
                'invoices.state as state',
                'invoices.base_grand_total as base_grand_total',
                'invoices.created_at as created_at'
            )
            ->selectRaw("CASE WHEN {$dbPrefix}invoices.increment_id IS NOT NULL THEN {$dbPrefix}invoices.increment_id ELSE {$dbPrefix}invoices.id END AS increment_id")
            ->selectRaw("(SELECT COALESCE(SUM(CASE WHEN ot.amount > 0 THEN ot.amount ELSE CAST(JSON_UNQUOTE(JSON_EXTRACT(ot.data, '$.amount')) AS DECIMAL(12,2)) END), 0) FROM {$dbPrefix}order_transactions as ot WHERE ot.order_id = {$dbPrefix}invoices.order_id OR ot.invoice_id = {$dbPrefix}invoices.id) as paid_amount")
            ->selectRaw("GREATEST(0, {$dbPrefix}invoices.base_grand_total - (SELECT COALESCE(SUM(CASE WHEN ot.amount > 0 THEN ot.amount ELSE CAST(JSON_UNQUOTE(JSON_EXTRACT(ot.data, '$.amount')) AS DECIMAL(12,2)) END), 0) FROM {$dbPrefix}order_transactions as ot WHERE ot.order_id = {$dbPrefix}invoices.order_id OR ot.invoice_id = {$dbPrefix}invoices.id)) as due_amount");

        $isProjects = request()->routeIs('hws.admin.projects.*') || str_contains(request()->path(), 'projects');
        if ($isProjects) {
            $queryBuilder->where('ors.sales_type', 'projects');
        } else {
            $queryBuilder->where('ors.sales_type', 'trading');
        }

        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($queryBuilder, 'invoices');

        $this->addFilter('increment_id', 'invoices.increment_id');
        $this->addFilter('order_id', 'ors.increment_id');
        $this->addFilter('base_grand_total', 'invoices.base_grand_total');
        $this->addFilter('created_at', 'invoices.created_at');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'increment_id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'order_id',
            'label'      => trans('admin::app.datagrid.order-id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.invoice-date'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                return $value->created_at ? \Carbon\Carbon::parse($value->created_at)->format('d M Y, h:i A') : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('admin::app.datagrid.grand-total'),
            'type'       => 'price',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'paid_amount',
            'label'      => 'Paid Amount',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($value) {
                return '<span style="color:#16a34a;font-weight:700;">' . core()->formatBasePrice((float) $value->paid_amount, true) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'due_amount',
            'label'      => 'Due Amount',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($value) {
                $due = isset($value->due_amount) ? (float) $value->due_amount : max(0, (float) $value->base_grand_total - (float) $value->paid_amount);
                $color = $due > 0 ? '#dc2626' : '#16a34a';
                return '<span style="color:' . $color . ';font-weight:700;">' . core()->formatBasePrice($due, true) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'state',
            'label'      => trans('admin::app.datagrid.status'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => function ($value) {
                $paid = (float) $value->paid_amount;
                $grandTotal = (float) $value->base_grand_total;

                if ($paid >= $grandTotal && $grandTotal > 0) {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.invoices.status-paid') . '</span>';
                } elseif ($paid > 0 && $paid < $grandTotal) {
                    return '<span class="badge badge-md badge-warning">Partially Paid</span>';
                } elseif ($value->state == 'overdue') {
                    return '<span class="badge badge-md badge-info">' . trans('admin::app.sales.invoices.status-overdue') . '</span>';
                } else {
                    return '<span class="badge badge-md badge-danger">Unpaid</span>';
                }
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.sales.invoices.view',
            'icon'   => 'icon eye-icon',
        ]);
    }
}
