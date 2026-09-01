<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\DataGrids\AttendanceDataGrid;

class AttendanceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return app(AttendanceDataGrid::class)->toJson();
        }

        return view('hws::admin.attendance.index');
    }
}
