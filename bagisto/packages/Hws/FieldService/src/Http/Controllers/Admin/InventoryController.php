<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\TaskMaterial;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Get aggregated material counts
        $aggregatedInventory = TaskMaterial::select('name', DB::raw('SUM(quantity) as total_consumed'))
            ->groupBy('name')
            ->orderByDesc('total_consumed')
            ->get();

        // Get detailed material usage log
        $usageLogs = TaskMaterial::with('task')
            ->orderByDesc('created_at')
            ->get();

        return view('hws::admin.inventory.index', compact('aggregatedInventory', 'usageLogs'));
    }
}
