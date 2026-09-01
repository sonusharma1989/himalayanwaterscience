<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class EmployeeDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'asc';

    public function prepareQueryBuilder()
    {
        $today = now()->toDateString();

        $queryBuilder = DB::table('admins')
            ->leftJoin('roles', 'roles.id', '=', 'admins.role_id')
            ->leftJoin('hws_attendance', function ($join) use ($today) {
                $join->on('hws_attendance.employee_id', '=', 'admins.id')
                    ->where('hws_attendance.date', '=', $today);
            })
            ->addSelect(
                'admins.id',
                'admins.name',
                'admins.email',
                'admins.status',
                'roles.name as role_name',
                'hws_attendance.check_in_time',
                'hws_attendance.check_out_time',
                DB::raw('(select concat(t.task_no, " (", t.type, ")") from hws_tasks as t where t.assigned_to = admins.id and t.step > 0 and t.step < 4 order by t.id desc limit 1) as active_task')
            );

        $this->addFilter('name', 'admins.name');
        $this->addFilter('email', 'admins.email');
        $this->addFilter('role_name', 'roles.name');
        $this->addFilter('status', 'admins.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Technician',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => 'Email',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'role_name',
            'label'      => 'Role',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return $row->role_name ?: 'N/A';
            },
        ]);

        $this->addColumn([
            'index'      => 'check_in_time',
            'label'      => "Today's Check In",
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->check_in_time && ! $row->check_out_time) {
                    return '<span style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:#059669;"><span style="width:8px;height:8px;border-radius:50%;background:#10b981;"></span>Checked In (' . \Carbon\Carbon::parse($row->check_in_time)->format('h:i A') . ')</span>';
                }

                return '<span style="color:#94a3b8;font-size:12.5px;">Not Checked In</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'active_task',
            'label'      => 'Active Task',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                return $row->active_task ?: 'None';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'checkbox',
            'options'    => [
                1 => 'Active',
                0 => 'Inactive',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if ($row->status) {
                    return '<span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;background:#d1fae5;color:#065f46;">Active</span>';
                }

                return '<span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;background:#fee2e2;color:#991b1b;">Inactive</span>';
            },
        ]);
    }

    public function prepareActions() {}
}
