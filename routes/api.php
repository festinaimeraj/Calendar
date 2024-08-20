<?php

use App\Http\Controllers\Api\AdminsController;
use App\Http\Controllers\Api\EditMyRequestsController;
use App\Http\Controllers\Api\MyLeaveTotalsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RequestLeaveController;
use App\Http\Controllers\Api\EmployeesController;
use App\Http\Controllers\Api\ApproveDenyRequestsController;
use App\Http\Controllers\Api\AdminReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/login', [UserController::class, 'loginUser']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/request-leave', [RequestLeaveController::class, 'index']);
    Route::post('/submit-leave-request', [RequestLeaveController::class, 'store']);

    Route::get('/my-leave-totals', [MyLeaveTotalsController::class, 'index']);
    Route::get('/my-leave-totals/{leaveType}', [MyLeaveTotalsController::class, 'showMyLeaveTotals']);

    Route::get('/edit-leave-request', [EditMyRequestsController::class, 'index']);
    Route::put('/edit-leave-request/{requestId}', [EditMyRequestsController::class, 'update']);

    Route::get('/employees', [EmployeesController::class, 'index']);
    Route::post('/employees', [EmployeesController::class, 'store']);
    Route::post('/employees/{id}', [EmployeesController::class, 'updateEmployee']);
    Route::post('/employees/{id}', [EmployeesController::class, 'destroy']);

    // Route::get('/admins', [AdminsController::class, 'index']);
    // Route::post('/admins', [AdminsController::class, 'store']);

    Route::get('/leave-requests/pending', [ApproveDenyRequestsController::class, 'index']);
    Route::post('/leave-requests/approve', [ApproveDenyRequestsController::class, 'approve']);
    Route::post('/leave-requests/deny', [ApproveDenyRequestsController::class, 'deny']);


    Route::post('/admin/report', [AdminReportController::class, 'search']);


});