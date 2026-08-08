<?php

use Illuminate\Support\Facades\Route;
use Hws\FieldService\Http\Controllers\Admin\ComingSoonController;
use Hws\FieldService\Http\Controllers\Admin\DashboardController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => config('app.admin_url', 'admin')], function () {

    Route::prefix('field-service')->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('hws.admin.dashboard.index');

        Route::get('employees', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Employees')
            ->name('hws.admin.employees.index');

        Route::get('attendance', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Attendance')
            ->name('hws.admin.attendance.index');

        Route::get('service-requests', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Service Requests')
            ->name('hws.admin.service-requests.index');

        Route::get('sales-leads', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Sales & Leads')
            ->name('hws.admin.sales-leads.index');

        Route::get('inventory', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Inventory')
            ->name('hws.admin.inventory.index');

        Route::get('expenses', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Expenses')
            ->name('hws.admin.expenses.index');

        Route::get('reports', [ComingSoonController::class, 'show'])
            ->defaults('title', 'Reports')
            ->name('hws.admin.reports.index');

    });

});
