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
use Hws\FieldService\Http\Controllers\Admin\QuotationController;
use Hws\FieldService\Http\Controllers\Admin\ProjectsController;
use Hws\FieldService\Http\Controllers\Admin\ProjectShipmentController;
use Webkul\Admin\Http\Controllers\Sales\OrderController;
use Webkul\Admin\Http\Controllers\Sales\InvoiceController;
use Webkul\Admin\Http\Controllers\Sales\ShipmentController;
use Webkul\Admin\Http\Controllers\Sales\RefundController;
use Webkul\Admin\Http\Controllers\Sales\TransactionController;

Route::group(['middleware' => ['web', 'admin'], 'prefix' => config('app.admin_url', 'admin')], function () {

    Route::prefix('projects')->group(function () {
        Route::get('dashboard', [ProjectsController::class, 'dashboard'])->name('hws.admin.projects.dashboard');
        Route::get('leads', [SalesLeadsController::class, 'index'])->name('hws.admin.projects.leads');

        Route::get('orders', [OrderController::class, 'index'])->defaults('_config', [
            'view' => 'hws::admin.projects.index', 'title' => 'Project Orders',
        ])->name('hws.admin.projects.orders');

        Route::get('invoices', [InvoiceController::class, 'index'])->defaults('_config', [
            'view' => 'hws::admin.projects.index', 'title' => 'Project Invoices',
        ])->name('hws.admin.projects.invoices');

        Route::get('shipments', [ShipmentController::class, 'index'])->defaults('_config', [
            'view' => 'hws::admin.projects.index', 'title' => 'Project Shipments',
        ])->name('hws.admin.projects.shipments');
        Route::get('shipments/create/{order_id}', [ProjectShipmentController::class, 'create'])->name('hws.admin.projects.shipments.create');
        Route::post('shipments/create/{order_id}', [ProjectShipmentController::class, 'store'])->name('hws.admin.projects.shipments.store');

        Route::get('refunds', [RefundController::class, 'index'])->defaults('_config', [
            'view' => 'hws::admin.projects.index', 'title' => 'Project Refunds',
        ])->name('hws.admin.projects.refunds');

        Route::get('transactions', [TransactionController::class, 'index'])->defaults('_config', [
            'view' => 'hws::admin.projects.index', 'title' => 'Project Transactions',
        ])->name('hws.admin.projects.transactions');
    });

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
        Route::get('sales-leads/{id}', [SalesLeadsController::class, 'show'])
            ->whereNumber('id')
            ->name('hws.admin.sales-leads.show');
        Route::post('sales-leads', [SalesLeadsController::class, 'store'])
            ->name('hws.admin.sales-leads.store');
        Route::post('sales-leads/{id}/edit', [SalesLeadsController::class, 'update'])
            ->name('hws.admin.sales-leads.update');
        Route::post('sales-leads/{id}/activity', [SalesLeadsController::class, 'logActivity'])
            ->name('hws.admin.sales-leads.activity');
        Route::post('sales-leads/{id}/patch', [SalesLeadsController::class, 'patchField'])
            ->name('hws.admin.sales-leads.patch');
        Route::post('sales-leads/{id}/convert-to-task', [SalesLeadsController::class, 'convertToTask'])
            ->name('hws.admin.sales-leads.convert-to-task');

        Route::get('sales-leads/{id}/quote', [QuotationController::class, 'create'])
            ->name('hws.admin.quotations.create');
        Route::post('quotations', [QuotationController::class, 'store'])
            ->name('hws.admin.quotations.store');
        Route::get('quotations/{id}/pdf', [QuotationController::class, 'downloadPdf'])
            ->name('hws.admin.quotations.pdf');
        Route::post('quotations/{id}/email', [QuotationController::class, 'sendEmail'])
            ->name('hws.admin.quotations.email');
        Route::post('quotations/{id}/convert-to-order', [QuotationController::class, 'convertToOrder'])
            ->name('hws.admin.quotations.convert-to-order');
        Route::post('orders/{id}/manual-payment', [QuotationController::class, 'recordManualPayment'])
            ->name('hws.admin.orders.manual-payment');

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
