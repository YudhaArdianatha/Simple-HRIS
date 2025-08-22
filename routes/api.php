<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Login route (tidak perlu auth)
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Semua route yang membutuhkan authentication
Route::middleware(['auth:sanctum'])->group(function(){
    
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['auth:sanctum', 'role:admin|manager'])->group(function(){
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::get('/users', [UserController::class, 'index']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function(){
    Route::apiResource('employees', EmployeeController::class)->except(['index']);
    Route::patch('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
    
    Route::apiResource('attendances', AttendanceController::class)->except(['index']);
    Route::patch('attendances/{id}/restore', [AttendanceController::class, 'restore'])->name('attendances.restore');

    Route::apiResource('leaves', LeaveController::class)->except(['index']);
    Route::patch('leaves/{id}/restore', [LeaveController::class, 'restore'])->name('leaves.restore');
    Route::post('/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

    Route::apiResource('users', UserController::class)->except(['index']);
    Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::post('users/{id}/assign-role', [UserController::class, 'assignRole'])->name('users.assignRole');
});

    // Routes untuk employee - PERBAIKAN: gunakan guard sanctum
    Route::middleware(['auth:sanctum' , 'role:admin|employee'])->group(function(){
        Route::get('/employees/{id}/leaves', [EmployeeController::class, 'getEmployeeLeaves']);
        Route::post('/employees/{id}/leaves', [EmployeeController::class, 'addEmployeeLeaves']);
        Route::get('/employees/{id}/attendances', [EmployeeController::class, 'getEmployeeAttendances']);
        Route::post('employees/{id}/attendances', [EmployeeController::class, 'addEmployeeAttendances']);
    });
});

// Route::middleware(['auth:sanctum'])->group(function(){
    
//     // Logout route
//     Route::post('/logout', [AuthController::class, 'logout']);

//     // Routes untuk admin
//     Route::middleware(['role:admin,sanctum'])->group(function(){
//         // HANYA name saja untuk employees (tidak termasuk index)
//         Route::apiResource('employees', EmployeeController::class)->except(['index']);
//         Route::patch('employees/{id}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
        
//         Route::apiResource('attendances', AttendanceController::class);
//         Route::patch('attendances/{id}/restore', [AttendanceController::class, 'restore'])->name('attendances.restore');

//         Route::apiResource('leaves', LeaveController::class);
//         Route::patch('leaves/{id}/restore', [LeaveController::class, 'restore'])->name('leaves.restore');
//         Route::post('/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
//         Route::post('/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

//         Route::apiResource('users', UserController::class);
//         Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
//         Route::post('users/{id}/assign-role', [UserController::class, 'assignRole'])->name('users.assignRole');
//     });

//     // Routes untuk manager - HANYA index
//     Route::middleware(['role:manager,sanctum'])->group(function(){
//         Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
//         Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
//         Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
//     });

//     // Routes untuk employee
//     Route::middleware(['role:employee,sanctum'])->group(function(){
//         Route::post('/employees/{id}/leaves', [EmployeeController::class, 'addEmployeeLeaves']);
//         Route::get('/employees/{id}/attendances', [EmployeeController::class, 'getEmployeeAttendances']);
//         Route::get('/employees/{id}/leaves', [EmployeeController::class, 'getEmployeeLeaves']);
//     });
// });


// Route::get('employees/{id}/attendances', [EmployeeController::class, 'getEmployeeAttendances'])->name('employees.attendances');
// Route::post('employees/{id}/attendances', [EmployeeController::class, 'addEmployeeAttendances'])->name('employees.attendances.add');


// Route::get('employees/{id}/leaves', [EmployeeController::class, 'getEmployeeLeaves'])->name('employees.leaves');
// Route::post('employees/{id}/leaves', [EmployeeController::class, 'addEmployeeLeaves'])->name('employees.leaves.add');