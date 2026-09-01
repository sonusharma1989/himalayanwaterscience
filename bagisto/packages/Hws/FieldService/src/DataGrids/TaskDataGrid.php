<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class TaskDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    protected $employees;

    protected $materialsByTask;

    protected $photosByTask;

    protected $stepLabels = [
        0 => 'Scheduled',
        1 => 'Started / Travel',
        2 => 'Job Commenced',
        3 => 'Sign-off Pending',
        4 => 'Completed',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->employees = DB::table('admins')->select('id', 'name')->get();

        $this->materialsByTask = DB::table('hws_task_materials')
            ->select('task_id', 'name', 'quantity')
            ->get()
            ->groupBy('task_id');

        $this->photosByTask = DB::table('hws_task_photos')
            ->select('task_id', 'file_path', 'type')
            ->get()
            ->groupBy('task_id');
    }

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('hws_tasks')
            ->whereIn('type', ['installation', 'amc_service', 'complaint', 'service'])
            ->addSelect(
                'id',
                'task_no',
                'type',
                'customer_name',
                'customer_phone',
                'customer_email',
                'customer_address',
                'source',
                'assigned_to',
                'priority',
                'step',
                'created_at',
                'scheduled_at'
            );

        $this->addFilter('id', 'id');
        $this->addFilter('task_no', 'task_no');
        $this->addFilter('customer_name', 'customer_name');
        $this->addFilter('type', 'type');
        $this->addFilter('step', 'step');
        $this->addFilter('priority', 'priority');
        $this->addFilter('scheduled_at', 'scheduled_at');

        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($queryBuilder, 'hws_tasks');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'created_at',
            'label'      => 'Date',
            'type'       => 'datetime',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'task_no',
            'label'      => 'Task No',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return '<div style="font-weight:700;color:#1e293b;">#' . e($row->task_no)
                    . ' <button type="button" onclick="document.getElementById(\'hwsEditTask' . $row->id . '\').style.display=\'flex\'" style="background:transparent;border:0;color:#3c50e0;cursor:pointer;font-size:11px;font-weight:700;">✎ Edit</button>'
                    . ' <button type="button" onclick="document.getElementById(\'hwsViewTask' . $row->id . '\').style.display=\'flex\'" style="background:transparent;border:0;color:#475569;cursor:pointer;font-size:11px;font-weight:700;">👁 Details</button></div>'
                    . $this->renderEditModal($row)
                    . $this->renderDetailsModal($row);
            },
        ]);

        $this->addColumn([
            'index'      => 'type',
            'label'      => 'Type',
            'type'       => 'checkbox',
            'options'    => [
                'service'      => 'Service',
                'installation' => 'Installation',
                'amc_service'  => 'AMC Service',
                'complaint'    => 'Complaint',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return '<span style="display:inline-flex;border-radius:6px;padding:4px 8px;font-size:11px;font-weight:700;background:#f1f5f9;color:#334155;">' . ucfirst(str_replace('_', ' ', $row->type)) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => 'Customer Details',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $html = '<div style="font-weight:600;color:#334155;">' . e($row->customer_name) . '</div>';
                $html .= '<div style="font-size:12px;color:#64748b;">📞 ' . e($row->customer_phone) . '</div>';

                if ($row->source) {
                    $html .= '<div style="font-size:10px;color:#ea7b32;font-weight:700;text-transform:uppercase;margin-top:3px;">' . e($row->source) . '</div>';
                }

                return $html;
            },
        ]);

        $this->addColumn([
            'index'      => 'assigned_to',
            'label'      => 'Assigned Technician',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false,
            'closure'    => function ($row) {
                $employee = $this->employees->firstWhere('id', $row->assigned_to);

                return $employee ? e($employee->name) : 'Unassigned';
            },
        ]);

        $this->addColumn([
            'index'      => 'priority',
            'label'      => 'Priority',
            'type'       => 'checkbox',
            'options'    => [
                'urgent' => 'Urgent',
                'high'   => 'High',
                'normal' => 'Normal',
                'low'    => 'Low',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $color = in_array($row->priority, ['urgent', 'high']) ? '#dc2626' : '#475569';

                return '<span style="font-weight:700;font-size:11px;text-transform:uppercase;color:' . $color . ';">' . e($row->priority) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'step',
            'label'      => 'Step / Status',
            'type'       => 'checkbox',
            'options'    => [
                0 => 'Scheduled',
                1 => 'Started / Travel',
                2 => 'Job Commenced',
                3 => 'Sign-off Pending',
                4 => 'Completed',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $isDone = (int) $row->step === 4;
                $background = $isDone ? '#d1fae5' : '#fffbeb';
                $color = $isDone ? '#065f46' : '#854d0e';
                $label = $this->stepLabels[$row->step] ?? 'Unknown';

                return '<span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;background:' . $background . ';color:' . $color . ';">' . $label . ' (Step ' . $row->step . '/4)</span>';
            },
        ]);
    }

    public function prepareActions() {}

    protected function renderEditModal($row)
    {
        $priorityOptions = '';
        foreach (['normal' => 'Normal', 'urgent' => 'Urgent', 'high' => 'High', 'low' => 'Low'] as $value => $label) {
            $selected = $row->priority === $value ? 'selected' : '';
            $priorityOptions .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
        }

        $stepOptions = '';
        foreach ($this->stepLabels as $value => $label) {
            $selected = (int) $row->step === $value ? 'selected' : '';
            $stepOptions .= '<option value="' . $value . '" ' . $selected . '>' . $value . ' · ' . $label . '</option>';
        }

        $assigneeOptions = '<option value="">Unassigned</option>';
        foreach ($this->employees as $employee) {
            $selected = (int) $row->assigned_to === (int) $employee->id ? 'selected' : '';
            $assigneeOptions .= '<option value="' . $employee->id . '" ' . $selected . '>' . e($employee->name) . '</option>';
        }

        return '<div id="hwsEditTask' . $row->id . '" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">'
            . '<div style="background:#fff;border-radius:16px;padding:24px;width:400px;max-width:92vw;text-align:left;">'
            . '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;"><h3 style="font-size:16px;font-weight:700;color:#334155;margin:0;">Edit Task #' . e($row->task_no) . '</h3>'
            . '<button type="button" onclick="document.getElementById(\'hwsEditTask' . $row->id . '\').style.display=\'none\'" style="background:transparent;border:0;color:#64748b;font-size:18px;cursor:pointer;font-weight:700;">×</button></div>'
            . '<form method="POST" action="' . route('hws.admin.service-requests.update', $row->id) . '">'
            . csrf_field()
            . '<div style="margin-bottom:16px;"><label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Assignee</label><select name="assigned_to" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:14px;background:#fff;">' . $assigneeOptions . '</select></div>'
            . '<div style="margin-bottom:16px;"><label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Priority</label><select name="priority" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:14px;background:#fff;">' . $priorityOptions . '</select></div>'
            . '<div style="margin-bottom:24px;"><label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">Workflow Step</label><select name="step" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:14px;background:#fff;">' . $stepOptions . '</select></div>'
            . '<div style="display:flex;justify-content:flex-end;gap:10px;">'
            . '<button type="button" onclick="document.getElementById(\'hwsEditTask' . $row->id . '\').style.display=\'none\'" style="background:#f1f5f9;color:#475569;border:0;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;">Cancel</button>'
            . '<button type="submit" style="background:#3c50e0;color:#fff;border:0;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;">Save Changes</button>'
            . '</div></form></div></div>';
    }

    protected function renderDetailsModal($row)
    {
        $materials = $this->materialsByTask->get($row->id, collect());
        $photos = $this->photosByTask->get($row->id, collect());

        $materialsHtml = $materials->isEmpty()
            ? '<p style="font-size:13px;color:#94a3b8;margin:0;">No materials used.</p>'
            : '<div style="display:flex;flex-wrap:wrap;gap:8px;">' . $materials->map(function ($mat) {
                return '<span style="background:#f1f5f9;border:1px solid #e2e8f0;border-radius:8px;padding:6px 12px;font-size:12.5px;font-weight:600;color:#475569;">' . e($mat->name) . ' x' . e($mat->quantity) . '</span>';
            })->implode('') . '</div>';

        $photosHtml = $photos->isEmpty()
            ? '<p style="font-size:13px;color:#94a3b8;margin:0;">No photos uploaded.</p>'
            : '<div style="display:flex;flex-wrap:wrap;gap:8px;">' . $photos->map(function ($photo) {
                $url = \Illuminate\Support\Facades\Storage::url($photo->file_path);

                return '<a href="' . $url . '" target="_blank" style="font-size:12px;font-weight:600;color:#3c50e0;background:#eff6ff;border-radius:6px;padding:6px 10px;">📷 ' . e($photo->type) . '</a>';
            })->implode('') . '</div>';

        return '<div id="hwsViewTask' . $row->id . '" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;overflow-y:auto;">'
            . '<div style="background:#fff;border-radius:16px;padding:28px;width:600px;max-width:92vw;margin:5vh auto;text-align:left;">'
            . '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">'
            . '<h3 style="font-size:18px;font-weight:800;color:#1e293b;margin:0;">Task Details #' . e($row->task_no) . '</h3>'
            . '<button type="button" onclick="document.getElementById(\'hwsViewTask' . $row->id . '\').style.display=\'none\'" style="background:transparent;border:0;color:#64748b;font-size:22px;cursor:pointer;font-weight:700;">×</button></div>'
            . '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:20px;">'
            . '<div><h4 style="font-size:11px;text-transform:uppercase;color:#94a3b8;margin:0 0 4px;font-weight:700;">Customer</h4><p style="font-weight:600;color:#334155;margin:0;">' . e($row->customer_name) . '</p><p style="font-size:13px;color:#64748b;margin:2px 0 0;">📞 ' . e($row->customer_phone) . '</p></div>'
            . '<div><h4 style="font-size:11px;text-transform:uppercase;color:#94a3b8;margin:0 0 4px;font-weight:700;">Address</h4><p style="font-size:13px;color:#334155;margin:0;">' . e($row->customer_address) . '</p></div>'
            . '</div>'
            . '<div style="margin-bottom:20px;"><h4 style="font-size:11px;text-transform:uppercase;color:#94a3b8;margin:0 0 8px;font-weight:700;">Materials Consumed</h4>' . $materialsHtml . '</div>'
            . '<div><h4 style="font-size:11px;text-transform:uppercase;color:#94a3b8;margin:0 0 8px;font-weight:700;">Service Photos</h4>' . $photosHtml . '</div>'
            . '</div></div>';
    }
}
