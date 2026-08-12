<?php

use Illuminate\Support\Facades\Route;
use Hws\FieldService\Http\Controllers\Admin\DashboardController;
use Hws\FieldService\Http\Controllers\Admin\EmployeesController;
use Hws\FieldService\Http\Controllers\Admin\AttendanceController;
use Hws\FieldService\Http\Controllers\Admin\ServiceRequestsController;
use Hws\FieldService\Http\Controllers\Admin\SalesLeadsController;
use Hws\FieldService\Http\Controllers\Admin\InventoryController;
use Hws\FieldService\Http\Controllers\Admin\ExpensesController;
use Hws\FieldService\Http\Controllers\Admin\ReportsController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => config('app.admin_url', 'admin')], function () {

    Route::prefix('field-service')->group(function () {

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('hws.admin.dashboard.index');

        Route::get('employees', [EmployeesController::class, 'index'])
            ->name('hws.admin.employees.index');

        Route::get('attendance', [AttendanceController::class, 'index'])
            ->name('hws.admin.attendance.index');

        Route::get('service-requests', [ServiceRequestsController::class, 'index'])
            ->name('hws.admin.service-requests.index');
        Route::post('service-requests', [ServiceRequestsController::class, 'store'])
            ->name('hws.admin.service-requests.store');
        Route::post('service-requests/{id}/edit', [ServiceRequestsController::class, 'update'])
            ->name('hws.admin.service-requests.update');

        Route::get('sales-leads', [SalesLeadsController::class, 'index'])
            ->name('hws.admin.sales-leads.index');
        Route::post('sales-leads', [SalesLeadsController::class, 'store'])
            ->name('hws.admin.sales-leads.store');
        Route::post('sales-leads/{id}/edit', [SalesLeadsController::class, 'update'])
            ->name('hws.admin.sales-leads.update');

        Route::get('inventory', [InventoryController::class, 'index'])
            ->name('hws.admin.inventory.index');

        Route::get('expenses', [ExpensesController::class, 'index'])
            ->name('hws.admin.expenses.index');
            
        Route::post('expenses/{id}/status', [ExpensesController::class, 'updateStatus'])
            ->name('hws.admin.expenses.status');

        Route::get('reports', [ReportsController::class, 'index'])
            ->name('hws.admin.reports.index');

    });

});
