<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Hws\FieldService\Models\Attendance;
use Illuminate\Support\Carbon;

$employeeId = 2; // The logged-in employee ID from the debug output
$today = Carbon::today()->toDateString();

echo "Querying for Employee ID: $employeeId, Date: $today\n";

$attendance = Attendance::where('employee_id', $employeeId)
    ->where('date', $today)
    ->first();

if ($attendance) {
    echo "Found Record!\n";
    echo "ID: " . $attendance->id . "\n";
    echo "Checked In: " . (!is_null($attendance->check_in_time) && is_null($attendance->check_out_time) ? 'Yes' : 'No') . "\n";
} else {
    echo "No Record Found!\n";
}
