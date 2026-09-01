<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\DataGrids\TaskMaterialDataGrid;
use Hws\FieldService\Models\TaskMaterial;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return app(TaskMaterialDataGrid::class)->toJson();
        }

        // Get aggregated material counts
        $aggregatedInventory = TaskMaterial::select('name', DB::raw('SUM(quantity) as total_consumed'))
            ->groupBy('name')
            ->orderByDesc('total_consumed')
            ->get();

        return view('hws::admin.inventory.index', compact('aggregatedInventory'));
    }
}
