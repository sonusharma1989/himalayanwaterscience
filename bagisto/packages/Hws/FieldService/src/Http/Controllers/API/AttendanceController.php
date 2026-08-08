<?php

namespace Hws\FieldService\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Hws\FieldService\Models\Attendance;

class AttendanceController extends Controller
{
    /**
     * Get today's attendance status.
     */
    public function today()
    {
        $employeeId = auth()->guard('admin-api')->id();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'checkedIn'         => false,
                'checkInTime'       => null,
                'checkOutTime'      => null,
                'checkInSelfieUrl'  => null,
                'checkOutSelfieUrl' => null,
            ]);
        }

        return response()->json([
            'checkedIn'         => !is_null($attendance->check_in_time) && is_null($attendance->check_out_time),
            'checkInTime'       => $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : null,
            'checkOutTime'      => $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : null,
            'checkInSelfieUrl'  => $attendance->check_in_selfie_path ? url('storage/' . $attendance->check_in_selfie_path) : null,
            'checkOutSelfieUrl' => $attendance->check_out_selfie_path ? url('storage/' . $attendance->check_out_selfie_path) : null,
        ]);
    }

    /**
     * Perform Check-in.
     */
    public function checkIn(Request $request)
    {
        $employeeId = auth()->guard('admin-api')->id();
        $today = Carbon::today()->toDateString();

        $validator = Validator::make($request->all(), [
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'selfie'     => 'nullable|image|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $existing = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Already checked in today.'], 400);
        }

        $selfiePath = null;
        if ($request->hasFile('selfie')) {
            $selfiePath = $request->file('selfie')->store('attendance/selfies', 'public');
        }

        $attendance = Attendance::create([
            'employee_id'          => $employeeId,
            'date'                 => $today,
            'check_in_time'        => Carbon::now(),
            'check_in_lat'         => $request->input('latitude'),
            'check_in_lng'         => $request->input('longitude'),
            'check_in_selfie_path' => $selfiePath,
        ]);

        \Hws\FieldService\Models\Notification::create([
            'admin_id' => $employeeId,
            'title'    => 'Checked In',
            'message'  => 'You successfully checked in at ' . Carbon::now()->format('h:i A') . '.',
            'is_read'  => false,
        ]);

        return response()->json([
            'message'           => 'Checked in successfully.',
            'checkInTime'       => $attendance->check_in_time->format('h:i A'),
            'checkedIn'         => true,
            'checkOutTime'      => null,
            'checkInSelfieUrl'  => $attendance->check_in_selfie_path ? url('storage/' . $attendance->check_in_selfie_path) : null,
            'checkOutSelfieUrl' => null,
        ]);
    }

    /**
     * Perform Check-out.
     */
    public function checkOut(Request $request)
    {
        $employeeId = auth()->guard('admin-api')->id();
        $today = Carbon::today()->toDateString();

        $validator = Validator::make($request->all(), [
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'selfie'    => 'nullable|image|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json(['error' => 'You must check-in before checking out.'], 400);
        }

        if ($attendance->check_out_time) {
            return response()->json(['error' => 'Already checked out today.'], 400);
        }

        $selfiePath = null;
        if ($request->hasFile('selfie')) {
            $selfiePath = $request->file('selfie')->store('attendance/selfies', 'public');
        }

        $attendance->update([
            'check_out_time'        => Carbon::now(),
            'check_out_lat'         => $request->input('latitude'),
            'check_out_lng'         => $request->input('longitude'),
            'check_out_selfie_path' => $selfiePath,
        ]);

        \Hws\FieldService\Models\Notification::create([
            'admin_id' => $employeeId,
            'title'    => 'Checked Out',
            'message'  => 'You successfully checked out at ' . Carbon::now()->format('h:i A') . '.',
            'is_read'  => false,
        ]);

        return response()->json([
            'message'           => 'Checked out successfully.',
            'checkInTime'       => $attendance->check_in_time->format('h:i A'),
            'checkOutTime'      => $attendance->check_out_time->format('h:i A'),
            'checkedIn'         => false,
            'checkInSelfieUrl'  => $attendance->check_in_selfie_path ? url('storage/' . $attendance->check_in_selfie_path) : null,
            'checkOutSelfieUrl' => $attendance->check_out_selfie_path ? url('storage/' . $attendance->check_out_selfie_path) : null,
        ]);
    }
}
