<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class TaskMaterialDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('hws_task_materials')
            ->leftJoin('hws_tasks', 'hws_tasks.id', '=', 'hws_task_materials.task_id')
            ->addSelect(
                DB::raw('ROW_NUMBER() OVER (ORDER BY hws_task_materials.id DESC) as sn'),
                'hws_task_materials.id',
                'hws_task_materials.name',
                'hws_task_materials.quantity',
                'hws_task_materials.created_at',
                'hws_tasks.task_no',
                'hws_tasks.customer_name'
            );

        $this->addFilter('name', 'hws_task_materials.name');
        $this->addFilter('task_no', 'hws_tasks.task_no');

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
            'index'      => 'name',
            'label'      => 'Material',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'quantity',
            'label'      => 'Quantity Used',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'task_no',
            'label'      => 'Used On Task',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return $row->task_no ? '#' . $row->task_no : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => 'Customer',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => 'Logged On',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') : '—';
            },
        ]);
    }

    public function prepareActions() {}
}
