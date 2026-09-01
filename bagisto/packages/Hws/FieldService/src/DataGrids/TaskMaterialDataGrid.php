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
            'type'       => 'datetime',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions() {}
}
