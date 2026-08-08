<?php

namespace Hws\FieldService\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Authenticate employee admin user.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        // Check if input is email or mobile (for login flexibility)
        if (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            // Assume it's a mobile number/username
            // Bagisto admins table by default uses email, let's allow custom logic if needed, but standard login uses email.
            // Let's attempt with email first.
        }

        if (!$token = auth()->guard('admin-api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password.'], 401);
        }

        $user = auth()->guard('admin-api')->user();

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role ? $user->role->name : null,
                'stats' => $this->getUserStats($user->id),
            ]
        ]);
    }

    /**
     * Get logged-in user profile.
     */
    public function profile()
    {
        $user = auth()->guard('admin-api')->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role ? $user->role->name : null,
            'stats' => $this->getUserStats($user->id),
        ]);
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        auth()->guard('admin-api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Calculate stats for the user.
     */
    private function getUserStats($userId)
    {
        $jobsCount = \Hws\FieldService\Models\Task::where('assigned_to', $userId)->where('step', 4)->count();
        $averageRating = round(\Hws\FieldService\Models\Task::where('assigned_to', $userId)->where('step', 4)->whereNotNull('rating')->avg('rating') ?? 5.0, 1);

        $firstAttendance = \Hws\FieldService\Models\Attendance::where('employee_id', $userId)->orderBy('date', 'asc')->first();
        if ($firstAttendance) {
            $daysSinceFirst = max(1, \Illuminate\Support\Carbon::parse($firstAttendance->date)->diffInDays(\Illuminate\Support\Carbon::now()) + 1);
            $totalDaysToCompare = min(30, $daysSinceFirst);
            $checkInsCount = \Hws\FieldService\Models\Attendance::where('employee_id', $userId)
                ->where('date', '>=', \Illuminate\Support\Carbon::now()->subDays(30)->toDateString())
                ->count();
            $workingDays = max(1, round($totalDaysToCompare * 0.85));
            $attendancePercentage = min(round(($checkInsCount / $workingDays) * 100), 100);
        } else {
            $attendancePercentage = 0;
        }

        return [
            'jobs'       => $jobsCount,
            'rating'     => $averageRating,
            'attendance' => $attendancePercentage,
        ];
    }
}
