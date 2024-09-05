<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Admin routes
// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/admin/employees', [AdminController::class, 'showEmployee'])->name('admin.employees');
    Route::get('/admin/employees', [AdminController::class, 'showEmployees'])->name('admin.employees');
    Route::get('/add-employee', [AdminController::class, 'addEmployee'])->name('admin.addEmployee');
    Route::post('/store-employee', [AdminController::class, 'storeEmployee'])->name('admin.storeEmployee');
    Route::get('/edit-employee/{id}', [AdminController::class, 'editEmployee'])->name('admin.editEmployee');
    Route::post('/update-employee/{id}', [AdminController::class, 'updateEmployee'])->name('admin.updateEmployee');
    Route::delete('/delete-employee/{id}', [AdminController::class, 'deleteEmployee'])->name('admin.deleteEmployee');
    Route::get('/admin/admins', [AdminController::class, 'showAdmin'])->name('admin.admins');
    Route::get('/add-admin', [AdminController::class, 'addAdmin'])->name('admin.addAdmin');
    Route::post('/store-admin', [AdminController::class, 'storeAdmin'])->name('admin.storeAdmin');
    Route::get('/edit-admin/{id}', [AdminController::class, 'editAdmin'])->name('admin.editAdmin');
    Route::post('/update-admin/{id}', [AdminController::class, 'updateAdmin'])->name('admin.updateAdmin');
    Route::delete('/delete-admin/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.deleteAdmin');
    Route::get('/request-leave', [AdminController::class, 'requestLeave'])->name('admin.request_leave');
    Route::post('/request-leave', [AdminController::class, 'submitLeaveRequest'])->name('admin.request_leave.submit');
    Route::get('/approve-deny-requests', [AdminController::class, 'approveDenyRequests'])->name('admin.approve-deny-requests');
    Route::post('/process-leave-request', [AdminController::class, 'processLeaveRequest'])->name('admin.processLeaveRequest');
    Route::get('/view-leave-reports', [AdminController::class, 'viewLeaveReports'])->name('admin.view-leave-reports');
    Route::prefix('leave-types')->group(function () {
        Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('admin.leave_types.index');
        Route::get('/create', [LeaveTypeController::class, 'create'])->name('admin.leave_types.create');
        Route::get('/edit/{id}', [LeaveTypeController::class, 'edit'])->name('admin.leave_types.edit');
        Route::get('/{id}', [LeaveTypeController::class, 'show'])->name('admin.leave_types.show');
        Route::post('/', [LeaveTypeController::class, 'store'])->name('admin.leave_types.store');
        Route::put('/{id}', [LeaveTypeController::class, 'update'])->name('admin.leave_types.update');
        Route::delete('/{id}', [LeaveTypeController::class, 'destroy'])->name('admin.leave_types.destroy');
    });
});

Route::middleware(['auth', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    Route::get('/calendar', [EmployeeController::class, 'calendar'])->name('calendar');
    Route::get('/request-leave', [EmployeeController::class, 'requestLeave'])->name('request_leave');
    Route::post('/request-leave', [EmployeeController::class, 'submitLeaveRequest'])->name('request_leave.submit'); 
    // Route::get('/my-leave-totals', [EmployeeController::class, 'myLeaveTotals'])->name('my-leave-totals');
    Route::get('/edit-my-requests', [EmployeeController::class, 'editMyRequests'])->name('edit-my-requests');
 
Route::post('/edit-my-requests', [LeaveRequestController::class, 'update'])->name('update-my-request');

    Route::get('/my-leave-totals', [LeaveController::class, 'showLeaveTotals'])->name('my-leave-totals');
});

// Calendar routes
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
Route::get('/load_events', [CalendarController::class, 'loadEvents'])->name('load_events');
Route::post('/update_event', [CalendarController::class, 'updateEvent'])->name('update_event');
Route::post('/delete_event', [CalendarController::class, 'deleteEvent'])->name('delete_event');
// Route::get('/fetch_leave_totals', [LeaveRequestController::class, 'fetchLeaveTotals'])->name('fetch_leave_totals');


