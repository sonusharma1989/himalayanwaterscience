<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class AttendanceDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('hws_attendance')
            ->leftJoin('admins', 'admins.id', '=', 'hws_attendance.employee_id')
            ->addSelect(
                DB::raw('ROW_NUMBER() OVER (ORDER BY hws_attendance.id DESC) as sn'),
                'hws_attendance.id',
                'hws_attendance.date',
                'hws_attendance.check_in_time',
                'hws_attendance.check_out_time',
                'admins.name as employee_name'
            );

        $this->addFilter('employee_name', 'admins.name');
        $this->addFilter('date', 'hws_attendance.date');

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
            'index'      => 'date',
            'label'      => 'Date',
            'type'       => 'date',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'check_in_time',
            'label'      => 'Check In',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($row) {
                return $row->check_in_time ? \Carbon\Carbon::parse($row->check_in_time)->format('h:i A') : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'check_out_time',
            'label'      => 'Check Out',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($row) {
                return $row->check_out_time ? \Carbon\Carbon::parse($row->check_out_time)->format('h:i A') : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Status',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->check_in_time && ! $row->check_out_time) {
                    return '<span style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:#059669;"><span style="width:8px;height:8px;border-radius:50%;background:#10b981;"></span>Checked In</span>';
                }

                if ($row->check_in_time && $row->check_out_time) {
                    return '<span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;background:#e0f2fe;color:#0369a1;">Day Complete</span>';
                }

                return '<span style="color:#94a3b8;font-size:12.5px;">Not Checked In</span>';
            },
        ]);
    }

    public function prepareActions() {}
}
