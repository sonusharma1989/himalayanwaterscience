<?php

namespace Hws\FieldService\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Hws\FieldService\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendanceRecords = Attendance::with('employee')
            ->orderByDesc('date')
            ->orderByDesc('check_in_time')
            ->get();

        return view('hws::admin.attendance.index', compact('attendanceRecords'));
    }
}
