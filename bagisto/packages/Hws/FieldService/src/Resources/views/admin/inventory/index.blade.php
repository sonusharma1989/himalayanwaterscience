@extends('hws::admin.layouts.menu')

@section('page_title')
    Material Inventory & Consumption
@stop

@section('page-content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Material & Inventory Consumption</h1>
            </div>
        </div>

        <div class="page-content" style="display: flex; flex-wrap: wrap; gap: 24px;">
            <div style="flex: 1; min-width: 280px; border: 1px solid #e2e8f0; background: #fff; border-radius: 8px; padding: 20px;">
                <h2 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 16px;">Consolidated Summary</h2>
                @if ($aggregatedInventory->isEmpty())
                    <p style="text-align: center; color: #94a3b8; padding: 20px 0; font-size: 14px;">No materials consumed yet.</p>
                @else
                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                        @foreach ($aggregatedInventory as $item)
                            <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 8px;">
                                <span style="font-weight: 600; color: #475569;">{{ $item->name }}</span>
                                <span style="background: #eff6ff; color: #3c50e0; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 100px;">
                                    {{ $item->total_consumed }} units
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div style="flex: 2; min-width: 450px;">
                <datagrid-plus src="{{ route('hws.admin.inventory.index') }}"></datagrid-plus>
            </div>
        </div>
    </div>
@stop
