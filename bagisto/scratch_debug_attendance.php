<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$attendance = \Hws\FieldService\Models\Attendance::all();
foreach ($attendance as $record) {
    echo "ID: " . $record->id . "\n";
    echo "Employee ID: " . $record->employee_id . "\n";
    echo "Date: " . $record->date . "\n";
    echo "Check In Time: " . $record->check_in_time . "\n";
    echo "Check Out Time: " . $record->check_out_time . "\n";
    echo "---------------------------------\n";
}
