<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\DataGrids\ExpenseClaimDataGrid;
use Hws\FieldService\Models\ExpenseClaim;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return app(ExpenseClaimDataGrid::class)->toJson();
        }

        return view('hws::admin.expenses.index');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $claim = ExpenseClaim::findOrFail($id);
        $claim->update([
            'status' => $request->status,
            'reviewed_by' => auth()->guard('admin')->user()->id,
        ]);

        session()->flash('success', 'Expense claim updated successfully.');

        return redirect()->back();
    }

    public function approve($id)
    {
        ExpenseClaim::findOrFail($id)->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->guard('admin')->user()->id,
        ]);

        return response()->json(['message' => 'Expense claim approved successfully.']);
    }

    public function reject($id)
    {
        ExpenseClaim::findOrFail($id)->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->guard('admin')->user()->id,
        ]);

        return response()->json(['message' => 'Expense claim rejected.']);
    }
}
