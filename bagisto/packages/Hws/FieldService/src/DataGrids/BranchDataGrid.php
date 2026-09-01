<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class BranchDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        $dbPrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('hws_branches')
            ->select(
                'hws_branches.id',
                'hws_branches.code',
                'hws_branches.name',
                'hws_branches.phone',
                'hws_branches.city',
                'hws_branches.state',
                'hws_branches.gstin',
                'hws_branches.is_head_office',
                'hws_branches.status',
                'hws_branches.created_at'
            )
            ->selectRaw("(SELECT COUNT(*) FROM {$dbPrefix}admins WHERE {$dbPrefix}admins.branch_id = {$dbPrefix}hws_branches.id) as staff_count")
            ->selectRaw("(SELECT COUNT(*) FROM {$dbPrefix}orders WHERE {$dbPrefix}orders.branch_id = {$dbPrefix}hws_branches.id) as total_orders")
            ->selectRaw("(SELECT COALESCE(SUM(base_grand_total), 0) FROM {$dbPrefix}orders WHERE {$dbPrefix}orders.branch_id = {$dbPrefix}hws_branches.id) as total_revenue");

        $this->addFilter('id', 'hws_branches.id');
        $this->addFilter('code', 'hws_branches.code');
        $this->addFilter('name', 'hws_branches.name');
        $this->addFilter('city', 'hws_branches.city');
        $this->addFilter('state', 'hws_branches.state');
        $this->addFilter('status', 'hws_branches.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'ID',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'code',
            'label'      => 'Branch Code',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                $hoBadge = $value->is_head_office 
                    ? '<span style="margin-left:6px;background:#e0e7ff;color:#3730a3;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;">HEAD OFFICE</span>' 
                    : '';
                return '<span style="font-weight:700;color:#1e293b;">' . e($value->code) . '</span>' . $hoBadge;
            },
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Branch Name',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'city',
            'label'      => 'City & State',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                return e($value->city ?: '—') . ($value->state ? ', ' . e($value->state) : '');
            },
        ]);

        $this->addColumn([
            'index'      => 'phone',
            'label'      => 'Phone',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'staff_count',
            'label'      => 'Staff',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($value) {
                return '<span style="display:inline-block;padding:2px 8px;background:#f1f5f9;color:#334155;border-radius:6px;font-weight:600;">' . $value->staff_count . ' Users</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'total_orders',
            'label'      => 'Orders',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'total_revenue',
            'label'      => 'Revenue',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($value) {
                return '<span style="color:#16a34a;font-weight:700;">' . core()->formatBasePrice((float) $value->total_revenue, true) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'checkbox',
            'options'    => [
                '1' => 'Active',
                '0' => 'Inactive',
            ],
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($value) {
                if ($value->status) {
                    return '<span class="badge badge-md badge-success">Active</span>';
                }
                return '<span class="badge badge-md badge-danger">Inactive</span>';
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => 'Edit Branch',
            'method' => 'GET',
            'route'  => 'hws.admin.branches.edit',
            'icon'   => 'icon pencil-lg-icon',
        ]);
    }
}
