<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Ui\DataGrid\DataGrid;

class OrderDataGrid extends DataGrid
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

        $queryBuilder = DB::table('orders')
            ->leftJoin('addresses as order_address_shipping', function ($leftJoin) {
                $leftJoin->on('order_address_shipping.order_id', '=', 'orders.id')
                    ->where('order_address_shipping.address_type', OrderAddress::ADDRESS_TYPE_SHIPPING);
            })
            ->leftJoin('addresses as order_address_billing', function ($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                    ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            })
            ->leftJoin('admins as account_managers', 'account_managers.id', '=', 'orders.account_manager_id')
            ->addSelect('orders.id', 'orders.increment_id', 'orders.base_sub_total', 'orders.base_grand_total', 'orders.created_at', 'orders.channel_name', 'orders.status', 'orders.sales_type', 'orders.account_manager_id')
            ->addSelect('account_managers.name as account_manager_name')
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name) as billed_to'))
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name) as shipped_to'))
            ->selectRaw("(SELECT COALESCE(SUM(CASE WHEN ot.amount > 0 THEN ot.amount ELSE CAST(JSON_UNQUOTE(JSON_EXTRACT(ot.data, '$.amount')) AS DECIMAL(12,2)) END), 0) FROM {$dbPrefix}order_transactions as ot WHERE ot.order_id = {$dbPrefix}orders.id) as paid_amount")
            ->selectRaw("GREATEST(0, {$dbPrefix}orders.base_grand_total - (SELECT COALESCE(SUM(CASE WHEN ot.amount > 0 THEN ot.amount ELSE CAST(JSON_UNQUOTE(JSON_EXTRACT(ot.data, '$.amount')) AS DECIMAL(12,2)) END), 0) FROM {$dbPrefix}order_transactions as ot WHERE ot.order_id = {$dbPrefix}orders.id)) as due_amount");

        $isProjects = request()->routeIs('hws.admin.projects.*') || str_contains(request()->path(), 'projects');
        if ($isProjects) {
            $queryBuilder->where('orders.sales_type', 'projects');
        } else {
            $queryBuilder->where('orders.sales_type', 'trading');
        }

        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($queryBuilder, 'orders');

        $this->addFilter('billed_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name)'));
        $this->addFilter('shipped_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name)'));
        $this->addFilter('increment_id', 'orders.increment_id');
        $this->addFilter('created_at', 'orders.created_at');
        $this->addFilter('status', 'orders.status');
        $this->addFilter('channel_name', 'orders.channel_name');
        $this->addFilter('sales_type', 'orders.sales_type');
        $this->addFilter('account_manager_name', 'account_managers.name');

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
            'index'      => 'base_sub_total',
            'label'      => trans('admin::app.datagrid.sub-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('admin::app.datagrid.grand-total'),
            'type'       => 'price',
            'searchable' => false,
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
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.order-date'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($value) {
                return $value->created_at ? \Carbon\Carbon::parse($value->created_at)->format('d M Y, h:i A') : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'account_manager_name',
            'label'      => 'Account Manager',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $employees = DB::table('admins')->select('id', 'name')->get();
                $optionsHtml = '<option value="">Unassigned</option>';
                foreach ($employees as $emp) {
                    $selected = ((string)$row->account_manager_id === (string)$emp->id) ? 'selected' : '';
                    $optionsHtml .= '<option value="' . e($emp->id) . '" ' . $selected . '>' . e($emp->name) . '</option>';
                }

                return '<select onchange="hwsQuickAssignOrderManager(' . $row->id . ', this.value, this)" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 12px; background: #f8fafc; font-weight: 600; color: #1e293b; cursor: pointer; outline: none; transition: border-color 0.2s;">'
                    . $optionsHtml
                    . '</select>';
            },
        ]);

        $this->addColumn([
            'index'      => 'channel_name',
            'label'      => trans('admin::app.datagrid.channel-name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('admin::app.datagrid.status'),
            'type'       => 'checkbox',
            'options'    => [
                'processing'      => trans('shop::app.customer.account.order.index.processing'),
                'completed'       => trans('shop::app.customer.account.order.index.completed'),
                'canceled'        => trans('shop::app.customer.account.order.index.canceled'),
                'closed'          => trans('shop::app.customer.account.order.index.closed'),
                'pending'         => trans('shop::app.customer.account.order.index.pending'),
                'pending_payment' => trans('shop::app.customer.account.order.index.pending-payment'),
                'fraud'           => trans('shop::app.customer.account.order.index.fraud'),
            ],
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure'    => function ($value) {
                if ($value->status == 'processing') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-processing') . '</span>';
                } elseif ($value->status == 'completed') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-success') . '</span>';
                } elseif ($value->status == 'canceled') {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-canceled') . '</span>';
                } elseif ($value->status == 'closed') {
                    return '<span class="badge badge-md badge-info">' . trans('admin::app.sales.orders.order-status-closed') . '</span>';
                } elseif ($value->status == 'pending') {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.sales.orders.order-status-pending') . '</span>';
                } elseif ($value->status == 'pending_payment') {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.sales.orders.order-status-pending-payment') . '</span>';
                } elseif ($value->status == 'fraud') {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-fraud') . '</span>';
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'sales_type',
            'label'      => 'Sales Type',
            'type'       => 'checkbox',
            'options'    => [
                'trading'  => 'Trading',
                'projects' => 'Projects',
                'services' => 'Services',
            ],
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($value) {
                $type = $value->sales_type ?: 'trading';
                $styles = [
                    'trading'  => ['#eff6ff', '#1d4ed8', '#bfdbfe'],
                    'projects' => ['#f5f3ff', '#7c3aed', '#ddd6fe'],
                    'services' => ['#ecfdf5', '#047857', '#a7f3d0'],
                ];
                [$background, $color, $border] = $styles[$type];

                return '<span style="display:inline-flex;align-items:center;border-radius:100px;padding:5px 10px;font-size:11px;font-weight:800;background:' . $background . ';color:' . $color . ';border:1px solid ' . $border . ';">' . ucfirst($type) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'billed_to',
            'label'      => trans('admin::app.datagrid.billed-to'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'shipped_to',
            'label'      => trans('admin::app.datagrid.shipped-to'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
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
            'route'  => 'admin.sales.orders.view',
            'icon'   => 'icon eye-icon',
        ]);
    }
}
