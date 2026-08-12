<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\ExpenseClaim;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseClaim::with(['employee', 'reviewer'])
            ->orderByDesc('created_at');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->get();

        return view('hws::admin.expenses.index', compact('claims'));
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
}
