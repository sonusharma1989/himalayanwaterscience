<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class ExpenseClaimDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('hws_expense_claims')
            ->leftJoin('admins', 'admins.id', '=', 'hws_expense_claims.employee_id')
            ->addSelect(
                DB::raw('ROW_NUMBER() OVER (ORDER BY hws_expense_claims.id DESC) as sn'),
                'hws_expense_claims.id',
                'hws_expense_claims.category',
                'hws_expense_claims.amount',
                'hws_expense_claims.description',
                'hws_expense_claims.receipt_path',
                'hws_expense_claims.status',
                'hws_expense_claims.created_at',
                'admins.name as employee_name'
            );

        $this->addFilter('employee_name', 'admins.name');
        $this->addFilter('category', 'hws_expense_claims.category');
        $this->addFilter('status', 'hws_expense_claims.status');

        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($queryBuilder, 'admins');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'sn',
            'label'      => 'S.No',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'employee_name',
            'label'      => 'Technician',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'category',
            'label'      => 'Category',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return ucfirst(str_replace('_', ' ', $row->category));
            },
        ]);

        $this->addColumn([
            'index'      => 'description',
            'label'      => 'Description',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'amount',
            'label'      => 'Amount',
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => 'Submitted',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'checkbox',
            'options'    => [
                'pending'  => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $styles = [
                    'pending'  => ['#fffbeb', '#854d0e'],
                    'approved' => ['#d1fae5', '#065f46'],
                    'rejected' => ['#fee2e2', '#991b1b'],
                ];
                $style = $styles[$row->status] ?? $styles['pending'];

                return '<span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;background:' . $style[0] . ';color:' . $style[1] . ';">' . ucfirst($row->status) . '</span>';
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon checkmark-icon',
            'title'  => 'Approve',
            'method' => 'POST',
            'route'  => 'hws.admin.expenses.approve',
        ]);

        $this->addAction([
            'icon'   => 'icon cancel-icon',
            'title'  => 'Reject',
            'method' => 'POST',
            'route'  => 'hws.admin.expenses.reject',
        ]);
    }
}
