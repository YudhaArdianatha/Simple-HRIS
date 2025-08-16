<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('employees', EmployeeController::class);
Route::patch('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

Route::apiResource('attendances', AttendanceController::class);
Route::patch('attendances/{id}/restore', [AttendanceController::class, 'restore'])->name('attendances.restore');

Route::get('employees/{id}/attendances', [EmployeeController::class, 'getEmployeeAttendances'])->name('employees.attendances');
Route::post('employees/{id}/attendances', [EmployeeController::class, 'addEmployeeAttendances'])->name('employees.attendances.add');

Route::apiResource('leaves', LeaveController::class);
Route::patch('leaves/{id}/restore', [LeaveController::class, 'restore'])->name('leaves.restore');

Route::get('employees/{id}/leaves', [EmployeeController::class, 'getEmployeeLeaves'])->name('employees.leaves');
Route::post('employees/{id}/leaves', [EmployeeController::class, 'addEmployeeLeaves'])->name('employees.leaves.add');