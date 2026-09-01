<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Hws\FieldService\Models\Branch;
use Hws\FieldService\DataGrids\BranchDataGrid;
use Webkul\User\Models\Admin;

class BranchController extends Controller
{
    /**
     * Display listing of branches
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(BranchDataGrid::class)->toJson();
        }

        return view('hws::admin.branches.index');
    }

    /**
     * Show branch create form
     */
    public function create()
    {
        $admins = Admin::where('status', 1)->get();
        
        $country = \Webkul\Core\Models\Country::where('code', 'IN')->first();
        $states = $country ? $country->states()->orderBy('code')->get()->map(function($state) {
            return (object) [
                'code' => $state->code,
                'name' => $state->default_name ?: $state->name ?: $state->code
            ];
        })->sortBy('name')->values() : collect();

        return view('hws::admin.branches.create', compact('admins', 'states'));
    }

    /**
     * Store new branch
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:hws_branches,code',
            'name'           => 'required|string|max:150',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'gstin'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:10',
            'is_head_office' => 'nullable|boolean',
            'status'         => 'required|boolean',
            'admin_ids'      => 'nullable|array',
        ]);

        if (!empty($data['is_head_office'])) {
            Branch::where('is_head_office', 1)->update(['is_head_office' => 0]);
        }

        $branch = Branch::create([
            'code'           => strtoupper(trim($data['code'])),
            'name'           => $data['name'],
            'phone'          => $data['phone'] ?? null,
            'email'          => $data['email'] ?? null,
            'gstin'          => $data['gstin'] ? strtoupper($data['gstin']) : null,
            'address'        => $data['address'] ?? null,
            'city'           => $data['city'] ?? null,
            'state'          => $data['state'] ?? null,
            'pincode'        => $data['pincode'] ?? null,
            'is_head_office' => !empty($data['is_head_office']) ? 1 : 0,
            'status'         => (int) $data['status'],
        ]);

        if (!empty($data['admin_ids'])) {
            Admin::whereIn('id', $data['admin_ids'])->update(['branch_id' => $branch->id]);
        }

        return redirect()->route('hws.admin.branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Show branch edit form
     */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        $admins = Admin::where('status', 1)->get();
        $assignedAdminIds = Admin::where('branch_id', $branch->id)->pluck('id')->toArray();
        
        $country = \Webkul\Core\Models\Country::where('code', 'IN')->first();
        $states = $country ? $country->states()->orderBy('code')->get()->map(function($state) {
            return (object) [
                'code' => $state->code,
                'name' => $state->default_name ?: $state->name ?: $state->code
            ];
        })->sortBy('name')->values() : collect();

        return view('hws::admin.branches.edit', compact('branch', 'admins', 'assignedAdminIds', 'states'));
    }

    /**
     * Update branch
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $data = $request->validate([
            'code'           => 'required|string|max:50|unique:hws_branches,code,' . $branch->id,
            'name'           => 'required|string|max:150',
            'phone'          => 'nullable|string|max:30',
            'email'          => 'nullable|email|max:100',
            'gstin'          => 'nullable|string|max:20',
            'address'        => 'nullable|string|max:500',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:10',
            'is_head_office' => 'nullable|boolean',
            'status'         => 'required|boolean',
            'admin_ids'      => 'nullable|array',
        ]);

        if (!empty($data['is_head_office'])) {
            Branch::where('id', '!=', $branch->id)->update(['is_head_office' => 0]);
        }

        $branch->update([
            'code'           => strtoupper(trim($data['code'])),
            'name'           => $data['name'],
            'phone'          => $data['phone'] ?? null,
            'email'          => $data['email'] ?? null,
            'gstin'          => $data['gstin'] ? strtoupper($data['gstin']) : null,
            'address'        => $data['address'] ?? null,
            'city'           => $data['city'] ?? null,
            'state'          => $data['state'] ?? null,
            'pincode'        => $data['pincode'] ?? null,
            'is_head_office' => !empty($data['is_head_office']) ? 1 : 0,
            'status'         => (int) $data['status'],
        ]);

        // Unlink old branch admins not in list
        $newAdminIds = $data['admin_ids'] ?? [];
        Admin::where('branch_id', $branch->id)->whereNotIn('id', $newAdminIds)->update(['branch_id' => null]);
        if (!empty($newAdminIds)) {
            Admin::whereIn('id', $newAdminIds)->update(['branch_id' => $branch->id]);
        }

        return redirect()->route('hws.admin.branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Switch active branch for Super Admin in session
     */
    public function switchBranch(Request $request)
    {
        if (!\Hws\FieldService\Helpers\BranchScopeHelper::isHeadOfficeUser()) {
            abort(403, 'Unauthorized: Only Head Office users can switch branches.');
        }

        $branchId = $request->input('branch_id');
        if ($branchId === 'all' || empty($branchId)) {
            session()->forget('hws_active_branch_id');
        } else {
            session(['hws_active_branch_id' => (int) $branchId]);
        }

        return redirect()->back()->with('success', 'Active branch view updated.');
    }
}
