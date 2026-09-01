<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class InvoicesTransactionsDatagrid extends DataGrid
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
        $invoice = DB::table('invoices')->where('id', request('id'))->first();
        $orderId = $invoice ? $invoice->order_id : null;

        $queryBuilder = DB::table('order_transactions')
            ->select(
                'order_transactions.id as id',
                'order_transactions.transaction_id as transaction_id',
                'order_transactions.payment_method as payment_method',
                'order_transactions.data as data',
                'order_transactions.created_at as created_at'
            )
            ->selectRaw('COALESCE(order_transactions.amount, 0) as amount')
            ->where(function ($query) use ($orderId) {
                $query->where('order_transactions.invoice_id', request('id'));
                if ($orderId) {
                    $query->orWhere('order_transactions.order_id', $orderId);
                }
            });

        $this->addFilter('id', 'order_transactions.id');
        $this->addFilter('transaction_id', 'order_transactions.transaction_id');
        $this->addFilter('created_at', 'order_transactions.created_at');

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
            'index'      => 'id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'transaction_id',
            'label'      => trans('admin::app.datagrid.transaction-id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'payment_method',
            'label'      => trans('admin::app.sales.orders.payment-method'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                return core()->getConfigData('sales.paymentmethods.' . $value->payment_method . '.title') 
                    ?: ucwords(str_replace('_', ' ', $value->payment_method ?: 'manual'));
            },
        ]);

        $this->addColumn([
            'index'      => 'amount',
            'label'      => trans('admin::app.sales.transactions.transaction-amount'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                $amt = (float) $value->amount;
                if ($amt <= 0 && $value->data) {
                    $json = is_array($value->data) ? $value->data : json_decode($value->data, true);
                    $amt = (float) ($json['amount'] ?? 0);
                }
                return core()->formatBasePrice($amt, true);
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.transaction-date'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                return $value->created_at ? \Carbon\Carbon::parse($value->created_at)->format('d M Y, h:i A') : '—';
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
            'route'  => 'admin.sales.transactions.view',
            'icon'   => 'icon eye-icon',
        ]);
    }
}
