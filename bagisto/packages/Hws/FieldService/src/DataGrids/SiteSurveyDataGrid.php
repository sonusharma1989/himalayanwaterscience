<?php

namespace Hws\FieldService\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class SiteSurveyDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    protected $employees;

    protected $salesType;

    public function __construct($salesType = null)
    {
        parent::__construct();

        $this->salesType = $salesType;
        $this->employees = DB::table('admins')->select('id', 'name')->get();
    }

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('hws_site_surveys')
            ->leftJoin('hws_tasks', 'hws_tasks.id', '=', 'hws_site_surveys.task_id')
            ->addSelect(
                DB::raw('ROW_NUMBER() OVER (ORDER BY hws_site_surveys.id DESC) as sn'),
                'hws_site_surveys.id',
                'hws_site_surveys.customer_name',
                'hws_site_surveys.customer_phone',
                'hws_site_surveys.customer_email',
                'hws_site_surveys.customer_address',
                'hws_site_surveys.property_type',
                'hws_site_surveys.request_type',
                'hws_site_surveys.reference_no',
                'hws_site_surveys.sales_type',
                'hws_site_surveys.assigned_to',
                'hws_site_surveys.temperature',
                'hws_site_surveys.status',
                'hws_site_surveys.source',
                'hws_site_surveys.created_at',
                'hws_tasks.task_no'
            );

        if ($this->salesType === 'projects') {
            $queryBuilder->where('hws_site_surveys.sales_type', 'projects');
        } elseif ($this->salesType === 'trading') {
            $queryBuilder->where('hws_site_surveys.sales_type', 'trading');
        } elseif ($this->salesType) {
            $queryBuilder->where('hws_site_surveys.sales_type', $this->salesType);
        }

        \Hws\FieldService\Helpers\BranchScopeHelper::applyScope($queryBuilder, 'hws_site_surveys');

        $this->addFilter('customer_name', 'hws_site_surveys.customer_name');
        $this->addFilter('status', 'hws_site_surveys.status');
        $this->addFilter('temperature', 'hws_site_surveys.temperature');
        $this->addFilter('sales_type', 'hws_site_surveys.sales_type');

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
            'index'      => 'created_at',
            'label'      => 'Created',
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') : '—';
            },
        ]);

        $this->addColumn([
            'index'      => 'reference_no',
            'label'      => 'Lead ID',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if ($row->reference_no) {
                    return $row->reference_no;
                }

                if ($row->task_no) {
                    return '#' . $row->task_no;
                }

                return '#SRV-' . $row->id;
            },
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => 'Customer',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if ($row->status === 'won') {
                    return '<div style="font-weight:600;color:#334155;">' . e($row->customer_name) . '</div><div style="font-size:12px;color:#64748b;">' . e($row->customer_phone) . '</div>';
                }

                $sourceOptions = ['Field Survey', 'Website', 'Website Checkout', 'Reference', 'Cold Call', 'Social Media'];
                $sourceOptionsHtml = '';
                foreach ($sourceOptions as $option) {
                    $selected = $row->source === $option ? 'selected' : '';
                    $sourceOptionsHtml .= '<option value="' . e($option) . '" ' . $selected . '>' . e($option) . '</option>';
                }

                $salesTypeOptions = ['trading' => 'Trading', 'projects' => 'Projects', 'services' => 'Services'];
                $salesTypeOptionsHtml = '';
                foreach ($salesTypeOptions as $value => $label) {
                    $selected = $row->sales_type === $value ? 'selected' : '';
                    $salesTypeOptionsHtml .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                }

                return '<div style="font-weight:600;color:#334155;">' . e($row->customer_name)
                    . ' <button type="button" onclick="document.getElementById(\'hwsEditLead' . $row->id . '\').style.display=\'flex\'" style="background:transparent;border:0;color:#3c50e0;cursor:pointer;font-size:11px;font-weight:700;">✎ Edit</button></div>'
                    . '<div style="font-size:12px;color:#64748b;">' . e($row->customer_phone) . '</div>'
                    . '<div id="hwsEditLead' . $row->id . '" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">'
                    . '<div style="background:#fff;border-radius:16px;padding:24px;width:550px;max-width:92vw;text-align:left;max-height:85vh;overflow-y:auto;">'
                    . '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;"><h3 style="font-size:16px;font-weight:700;color:#334155;margin:0;">Update Lead</h3>'
                    . '<button type="button" onclick="document.getElementById(\'hwsEditLead' . $row->id . '\').style.display=\'none\'" style="background:transparent;border:0;color:#64748b;font-size:18px;cursor:pointer;font-weight:700;">×</button></div>'
                    . '<form onsubmit="hwsSubmitEditLead(event, this, ' . $row->id . ')" method="POST" action="' . route('hws.admin.sales-leads.update', $row->id) . '">'
                    . csrf_field()
                    . '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">'
                    . '<div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Customer Name</label><input type="text" name="customer_name" value="' . e($row->customer_name) . '" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;"/></div>'
                    . '<div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Customer Phone</label><input type="text" name="customer_phone" value="' . e($row->customer_phone) . '" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;"/></div>'
                    . '<div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Customer Email</label><input type="email" name="customer_email" value="' . e($row->customer_email) . '" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;"/></div>'
                    . '<div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Lead Source</label><select name="source" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;background:#fff;">' . $sourceOptionsHtml . '</select></div>'
                    . '<div><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Sales Type</label><select name="sales_type" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;background:#fff;">' . $salesTypeOptionsHtml . '</select></div>'
                    . '<div style="grid-column:span 2;"><label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:4px;">Customer Address</label><textarea name="customer_address" required rows="2" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;font-size:13px;">' . e($row->customer_address) . '</textarea></div>'
                    . '</div>'
                    . '<input type="hidden" name="status" value="' . e($row->status) . '"/>'
                    . '<input type="hidden" name="temperature" value="' . e($row->temperature) . '"/>'
                    . '<input type="hidden" name="assigned_to" value="' . e($row->assigned_to) . '"/>'
                    . '<div style="display:flex;justify-content:flex-end;gap:10px;">'
                    . '<button type="button" onclick="document.getElementById(\'hwsEditLead' . $row->id . '\').style.display=\'none\'" style="background:#f1f5f9;color:#475569;border:0;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;font-size:13px;">Cancel</button>'
                    . '<button type="submit" style="background:#3c50e0;color:#fff;border:0;padding:8px 16px;border-radius:6px;font-weight:600;cursor:pointer;font-size:13px;">Save Changes</button>'
                    . '</div></form></div></div>';
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
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $styles = [
                    'trading'  => ['#eff6ff', '#1d4ed8'],
                    'projects' => ['#f5f3ff', '#7c3aed'],
                    'services' => ['#ecfdf5', '#047857'],
                ];
                $type  = $row->sales_type ?: 'trading';
                $style = $styles[$type] ?? $styles['trading'];

                return '<span style="display:inline-flex;border-radius:100px;padding:5px 10px;font-size:11px;font-weight:800;background:' . $style[0] . ';color:' . $style[1] . ';">' . ucfirst($type) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'assigned_to',
            'label'      => 'Assigned Agent',
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
            'index'      => 'temperature',
            'label'      => 'Lead Type',
            'type'       => 'checkbox',
            'options'    => [
                'hot'  => 'Hot',
                'warm' => 'Warm',
                'cold' => 'Cold',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $labels = [
                    'hot'  => '<span style="color:#ef4444;font-weight:700;">🔥 HOT</span>',
                    'warm' => '<span style="color:#f59e0b;font-weight:700;">⚡ WARM</span>',
                    'cold' => '<span style="color:#3b82f6;font-weight:700;">❄ COLD</span>',
                ];

                return $labels[$row->temperature] ?? $labels['warm'];
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => 'Pipeline Status',
            'type'       => 'checkbox',
            'options'    => [
                'new'           => 'New',
                'contacted'     => 'Contacted',
                'proposal_sent' => 'Proposal Sent',
                'negotiation'   => 'Negotiation',
                'won'           => 'Won',
                'lost'          => 'Lost',
            ],
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $styles = [
                    'won'  => ['#d1fae5', '#065f46'],
                    'lost' => ['#fee2e2', '#991b1b'],
                ];
                $style = $styles[$row->status] ?? ['#fffbeb', '#854d0e'];

                return '<span style="display:inline-flex;border-radius:100px;padding:4px 10px;font-size:11px;font-weight:700;background:' . $style[0] . ';color:' . $style[1] . ';">' . strtoupper(str_replace('_', ' ', $row->status)) . '</span>';
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => 'View Details',
            'method' => 'GET',
            'route'  => 'hws.admin.sales-leads.show',
            'icon'   => 'icon eye-icon',
        ]);
    }
}
