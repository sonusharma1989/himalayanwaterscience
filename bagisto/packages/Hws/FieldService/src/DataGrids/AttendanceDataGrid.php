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
                'hws_attendance.check_in_lat',
                'hws_attendance.check_in_lng',
                'hws_attendance.check_in_selfie_path',
                'hws_attendance.check_out_time',
                'hws_attendance.check_out_lat',
                'hws_attendance.check_out_lng',
                'hws_attendance.check_out_selfie_path',
                'admins.name as employee_name'
            );

        $this->addFilter('employee_name', 'admins.name');
        $this->addFilter('date', 'hws_attendance.date');

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
            'index'      => 'check_in_selfie',
            'label'      => 'In Selfie',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->check_in_selfie_path) {
                    $url = asset('storage/' . $row->check_in_selfie_path);
                    return '<a href="' . $url . '" data-fancybox="attendance-gallery" data-caption="Check-in: ' . e($row->employee_name) . ' (' . ($row->check_in_time ? \Carbon\Carbon::parse($row->check_in_time)->format('d M Y h:i A') : '') . ')">
                        <img src="' . $url . '" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1.5px solid #06b6d4;cursor:pointer;box-shadow:0 2px 4px rgba(0,0,0,0.1);transition:transform 0.2s;" onmouseover="this.style.transform=\'scale(1.08)\'" onmouseout="this.style.transform=\'scale(1)\'" alt="Check In Selfie" />
                    </a>';
                }
                return '<span style="color:#94a3b8;font-size:12px;">—</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'check_in_location',
            'label'      => 'In Location',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->check_in_lat && $row->check_in_lng) {
                    $mapUrl = 'https://www.google.com/maps?q=' . $row->check_in_lat . ',' . $row->check_in_lng;
                    return '<a href="' . $mapUrl . '" target="_blank" style="display:inline-flex;align-items:center;gap:4px;color:#0284c7;font-weight:600;font-size:11.5px;text-decoration:none;background:#f0f9ff;padding:4px 8px;border-radius:6px;border:1px solid #bae6fd;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Map
                    </a>';
                }
                return '<span style="color:#94a3b8;font-size:12px;">—</span>';
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
            'index'      => 'check_out_selfie',
            'label'      => 'Out Selfie',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->check_out_selfie_path) {
                    $url = asset('storage/' . $row->check_out_selfie_path);
                    return '<a href="' . $url . '" data-fancybox="attendance-gallery" data-caption="Check-out: ' . e($row->employee_name) . ' (' . ($row->check_out_time ? \Carbon\Carbon::parse($row->check_out_time)->format('d M Y h:i A') : '') . ')">
                        <img src="' . $url . '" style="width:42px;height:42px;object-fit:cover;border-radius:8px;border:1.5px solid #f59e0b;cursor:pointer;box-shadow:0 2px 4px rgba(0,0,0,0.1);transition:transform 0.2s;" onmouseover="this.style.transform=\'scale(1.08)\'" onmouseout="this.style.transform=\'scale(1)\'" alt="Check Out Selfie" />
                    </a>';
                }
                return '<span style="color:#94a3b8;font-size:12px;">—</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'check_out_location',
            'label'      => 'Out Location',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if ($row->check_out_lat && $row->check_out_lng) {
                    $mapUrl = 'https://www.google.com/maps?q=' . $row->check_out_lat . ',' . $row->check_out_lng;
                    return '<a href="' . $mapUrl . '" target="_blank" style="display:inline-flex;align-items:center;gap:4px;color:#d97706;font-weight:600;font-size:11.5px;text-decoration:none;background:#fef3c7;padding:4px 8px;border-radius:6px;border:1px solid #fde68a;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Map
                    </a>';
                }
                return '<span style="color:#94a3b8;font-size:12px;">—</span>';
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
                    return '<span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#059669;background:#ecfdf5;padding:4px 10px;border-radius:999px;border:1px solid #a7f3d0;"><span style="width:7px;height:7px;border-radius:50%;background:#10b981;"></span>Checked In</span>';
                }

                if ($row->check_in_time && $row->check_out_time) {
                    return '<span style="display:inline-flex;align-items:center;border-radius:999px;padding:4px 10px;font-size:11.5px;font-weight:700;background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;">Day Complete</span>';
                }

                return '<span style="color:#94a3b8;font-size:12px;">Not Checked In</span>';
            },
        ]);
    }

    public function prepareActions() {}
}
