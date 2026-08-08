<?php

use Illuminate\Support\Facades\Route;
use Hws\FieldService\Http\Controllers\API\AuthController;
use Hws\FieldService\Http\Controllers\API\AttendanceController;
use Hws\FieldService\Http\Controllers\API\TaskController;
use Hws\FieldService\Http\Controllers\API\SurveyController;
use Hws\FieldService\Http\Controllers\API\NotificationController;

Route::group(['prefix' => 'api/v1/employee'], function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => 'auth:admin-api'], function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('attendance/today', [AttendanceController::class, 'today']);
        Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);

        Route::get('tasks', [TaskController::class, 'index']);
        Route::post('tasks/{id}/step', [TaskController::class, 'updateStep']);
        Route::post('tasks/{id}/survey', [SurveyController::class, 'submitSurvey']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/mark-read', [NotificationController::class, 'markRead']);
    });

});
