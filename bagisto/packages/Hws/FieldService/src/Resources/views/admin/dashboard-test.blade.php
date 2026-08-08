<!DOCTYPE html>
<html>
<head><title>HWS Diagnostic</title></head>
<body style="font-family:sans-serif;padding:40px;background:#fff;color:#000;">
    <h1 style="color:green;">✓ IF YOU SEE THIS TEXT, THE ROUTE AND CONTROLLER WORK.</h1>
    <p>This means the blank screen is specifically caused by <code>@extends('admin::layouts.master')</code> — not routing, not the controller, not permissions.</p>
    <p>Employees online: {{ $employeesOnline ?? 'no data passed' }}</p>
    <p>Pending jobs: {{ $pendingJobs ?? 'no data passed' }}</p>
</body>
</html>
