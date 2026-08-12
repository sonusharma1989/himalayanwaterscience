<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\SiteSurvey;
use Hws\FieldService\Models\LeadActivity;
use Hws\FieldService\Models\Quotation;
use Hws\FieldService\Models\QuotationItem;
use Hws\FieldService\Models\Task;
use Webkul\User\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesLeadsController extends Controller
{
    /**
     * Display a listing of site surveys / sales leads.
     */
    public function index(Request $request)
    {
        $query = SiteSurvey::with(['task', 'inquiryTypes'])->orderByDesc('created_at');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('temperature')) {
            $query->where('temperature', $request->temperature);
        }

        $leads = $query->get();
        $employees = Admin::all();

        return view('hws::admin.sales-leads.index', compact('leads', 'employees'));
    }

    /**
     * Create a new independent sales lead.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'property_type'    => 'required|in:hotel,hospital,bungalow,other',
            'temperature'      => 'required|in:hot,warm,cold',
            'assigned_to'      => 'nullable|exists:admins,id',
            'source'           => 'nullable|string|max:100',
            'next_follow_up_at'=> 'nullable|date',
        ]);

        SiteSurvey::create([
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'customer_email'   => $request->customer_email,
            'customer_address' => $request->customer_address,
            'property_type'    => $request->property_type,
            'temperature'      => $request->temperature,
            'assigned_to'      => $request->assigned_to ?: null,
            'source'           => $request->source ?: 'Field Survey',
            'next_follow_up_at'=> $request->next_follow_up_at ?: null,
            'status'           => 'new',
        ]);

        session()->flash('success', 'Lead created successfully.');

        return redirect()->back();
    }

    /**
     * Update Lead Status, Assignee, Temperature, etc.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'status'           => 'required|in:new,contacted,proposal_sent,negotiation,won,lost',
            'temperature'      => 'required|in:hot,warm,cold',
            'assigned_to'      => 'nullable|exists:admins,id',
            'source'           => 'nullable|string|max:100',
            'next_follow_up_at'=> 'nullable|date',
        ]);

        $lead = SiteSurvey::findOrFail($id);
        
        if ($lead->status === 'won') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Updates are disabled because this lead has already been won!'
                ], 422);
            }
            session()->flash('error', 'Updates are disabled because this lead has already been won!');
            return redirect()->back();
        }

        $data = $request->only([
            'customer_name',
            'customer_phone',
            'customer_email',
            'customer_address',
            'status',
            'temperature',
            'assigned_to',
            'source',
            'next_follow_up_at'
        ]);

        if (empty($data['assigned_to'])) {
            $data['assigned_to'] = null;
        }
        if (empty($data['next_follow_up_at'])) {
            $data['next_follow_up_at'] = null;
        }

        // Track changes for timeline logging
        $changes = [];
        if ($lead->status !== $data['status']) {
            $changes[] = "Lead stage updated from '" . strtoupper($lead->status) . "' to '" . strtoupper($data['status']) . "'";
        }
        if ($lead->temperature !== $data['temperature']) {
            $changes[] = "Lead temperature updated from '" . strtoupper($lead->temperature) . "' to '" . strtoupper($data['temperature']) . "'";
        }
        if ($lead->assigned_to != $data['assigned_to']) {
            $oldAgentName = $lead->assigned_to ? Admin::find($lead->assigned_to)->name : 'Unassigned';
            $newAgentName = $data['assigned_to'] ? Admin::find($data['assigned_to'])->name : 'Unassigned';
            $changes[] = "Lead assignee changed from '{$oldAgentName}' to '{$newAgentName}'";
        }


        $lead->update($data);

        // Write change logs
        foreach ($changes as $change) {
            LeadActivity::create([
                'survey_id'     => $lead->id,
                'action_by'     => auth()->guard('admin')->id() ?? 1,
                'activity_type' => 'note',
                'notes'         => $change,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully.'
            ]);
        }

        session()->flash('success', 'Lead updated successfully.');

        return redirect()->back();
    }

    /**
     * Log a sales activity/notes for a Lead.
     */
    public function logActivity(Request $request, $id)
    {
        $request->validate([
            'activity_type'     => 'required|in:call,email,meeting,note,whatsapp',
            'notes'             => 'required|string',
            'next_follow_up_at' => 'nullable|date',
        ]);

        $lead = SiteSurvey::findOrFail($id);

        if ($lead->status === 'won') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logging new interactions is disabled because this lead has already been won!'
                ], 422);
            }
            session()->flash('error', 'Logging new interactions is disabled because this lead has already been won!');
            return redirect()->back();
        }

        $activity = LeadActivity::create([
            'survey_id'     => $id,
            'action_by'     => auth()->guard('admin')->id() ?? 1,
            'activity_type' => $request->activity_type,
            'notes'         => $request->notes,
        ]);

        if ($request->filled('next_follow_up_at')) {
            $lead->update([
                'next_follow_up_at' => $request->next_follow_up_at
            ]);

            // Log Follow-up action dynamically in timeline
            LeadActivity::create([
                'survey_id'     => $id,
                'action_by'     => auth()->guard('admin')->id() ?? 1,
                'activity_type' => 'note',
                'notes'         => 'Follow-up scheduled for: ' . date('M d, Y h:i A', strtotime($request->next_follow_up_at)),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity logged successfully.',
                'activity' => [
                    'created_at' => $activity->created_at->format('M d, Y h:i A'),
                    'activity_type' => strtoupper($activity->activity_type),
                    'notes' => $activity->notes,
                    'admin_name' => auth()->guard('admin')->user()->name ?? 'System',
                ]
            ]);
        }

        session()->flash('success', 'Activity logged successfully.');

        return redirect()->back();
    }

    /**
     * Convert Won Lead to an active Installation/Repair Task.
     */
    public function convertToTask(Request $request, $id)
    {
        $lead = SiteSurvey::findOrFail($id);

        DB::beginTransaction();

        try {
            // Check if task already exists
            if ($lead->task_id) {
                return redirect()->back()->withErrors(['error' => 'Lead is already converted/linked to a task.']);
            }

            $task = Task::create([
                'task_no'          => 'TSK-' . rand(1000, 9999),
                'type'             => 'installation',
                'customer_name'    => $lead->customer_name,
                'customer_phone'   => $lead->customer_phone,
                'customer_address' => $lead->customer_address,
                'priority'         => 'high',
                'step'             => 0, // Scheduled
                'scheduled_at'     => now(),
                'assigned_to'      => $lead->assigned_to,
            ]);

            $lead->update(['task_id' => $task->id]);

            DB::commit();

            session()->flash('success', 'Lead successfully converted to Active Service Task #' . $task->task_no);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to convert lead to task.']);
        }

        return redirect()->back();
    }

    /**
     * Patch a single field (status, temperature, next_follow_up_at) dynamically via AJAX.
     */
    public function patchField(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|in:status,temperature,next_follow_up_at,assigned_to',
            'value' => 'nullable',
        ]);

        $lead = SiteSurvey::findOrFail($id);

        if ($lead->status === 'won') {
            return response()->json([
                'success' => false,
                'message' => 'Updates are disabled because this lead has already been won!'
            ], 422);
        }

        $field = $request->field;
        $value = $request->value;

        if (empty($value)) {
            $value = null;
        }

        $oldValue = $lead->{$field};

        if ($oldValue == $value) {
            return response()->json(['success' => true, 'message' => 'No changes made.']);
        }

        // Track and log the change note
        $note = "";
        if ($field === 'status') {
            $note = "Lead stage updated from '" . strtoupper($oldValue ?? 'NEW') . "' to '" . strtoupper($value) . "'";
            $lead->status = $value;
        } elseif ($field === 'temperature') {
            $note = "Lead temperature updated from '" . strtoupper($oldValue ?? 'WARM') . "' to '" . strtoupper($value) . "'";
            $lead->temperature = $value;
        } elseif ($field === 'next_follow_up_at') {
            if ($value) {
                $note = "Follow-up reminder scheduled";
            } else {
                $note = "Follow-up reminder removed";
            }
            $lead->next_follow_up_at = $value;
        } elseif ($field === 'assigned_to') {
            $oldAgentName = $oldValue ? Admin::find($oldValue)->name : 'Unassigned';
            $newAgentName = $value ? Admin::find($value)->name : 'Unassigned';
            $note = "Lead assignee changed from '{$oldAgentName}' to '{$newAgentName}'";
            $lead->assigned_to = $value;
        }

        $lead->save();

        if ($note) {
            LeadActivity::create([
                'survey_id'     => $lead->id,
                'action_by'     => auth()->guard('admin')->id() ?? 1,
                'activity_type' => 'note',
                'notes'         => $note,
            ]);
        }

        // Get updated timeline HTML to reload on front-end
        $activities = DB::table('hws_lead_activities')
            ->leftJoin('admins', 'hws_lead_activities.action_by', '=', 'admins.id')
            ->where('hws_lead_activities.survey_id', $lead->id)
            ->select('hws_lead_activities.*', 'admins.name as admin_name')
            ->orderByDesc('hws_lead_activities.created_at')
            ->get();

        $timelineHtml = "";
        foreach($activities as $act) {
            $formattedTime = date('M d, Y h:i A', strtotime($act->created_at));
            $adminName = $act->admin_name ?? 'System';
            $actType = strtoupper($act->activity_type);
            $timelineHtml .= "
                <div style=\"position: relative; margin-bottom: 16px;\">
                    <div style=\"position: absolute; left: -22px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: #3c50e0;\"></div>
                    <span style=\"font-size: 11px; font-weight: 700; color: #94a3b8;\">
                        {$formattedTime} | By: {$adminName} | Type: {$actType}
                    </span>
                    <p style=\"margin: 4px 0 0; color: #334155; font-size: 13.5px; font-weight: 500;\">
                        {$act->notes}
                    </p>
                </div>
            ";
        }

        return response()->json([
            'success' => true,
            'message' => ucfirst($field) . ' updated successfully.',
            'timelineHtml' => $timelineHtml,
            'status' => $lead->status,
            'temperature' => $lead->temperature,
            'next_follow_up_at' => $lead->next_follow_up_at ? date('d M, Y h:i A', strtotime($lead->next_follow_up_at)) : 'No reminder set'
        ]);
    }
}
