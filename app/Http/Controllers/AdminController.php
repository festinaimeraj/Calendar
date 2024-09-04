<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;
use App\Models\LeaveType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function calendar()
    {
        return view('admin.calendar');
    }

    public function showEmployees()
    {
        $employees = User::leftJoin('leave_requests', 'users.id', '=', 'leave_requests.user_id')
            ->where('users.role', 'employee')
            ->select('users.id', 'users.username', 'users.name', 'users.surname', 'users.email')
            ->selectRaw('SUM(CASE WHEN leave_requests.answer = "approved" AND leave_requests.leave_type = 1 THEN DATEDIFF(leave_requests.end_date, leave_requests.start_date) + 1 ELSE 0 END) AS total_days_used')
            ->groupBy('users.id', 'users.username', 'users.name', 'users.surname', 'users.email')
            ->get();

        return view('admin.employees', compact('employees'));
    }

    public function addEmployee()
    {
        return view('admin.addEmployee');
    }

    public function storeEmployee(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required',
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee added successfully.');
    }

    public function editEmployee($id)
    {
        $employee = User::findOrFail($id);
        return view('admin.editEmployee', compact('employee'));
    }

    public function updateEmployee(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        $employee = User::findOrFail($id);
        $employee->update([
            'username' => $request->username,
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully.');
    }

    public function showAdmin()
    {
        $admins = User::where('role', 'admin')->get();
        return view('admin.admins', compact('admins'));
    }

    public function addAdmin()
    {
        return view('admin.addAdmin');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->route('admin.admins')->with('success', 'Admin added successfully.');
    }

    
    public function editAdmin($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.editAdmin', compact('admin'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$id,
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        $admin = User::findOrFail($id);
        $admin->update([
            'username' => $request->username,
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.admins')->with('success', 'Admin updated successfully.');
    }

    public function requestLeave()
    {
        $employees = User::where('role', 'employee')->get();
        $leaveTypes = LeaveType::all();

        return view('admin.request_leave', ['employees' => $employees, 'leaveTypes' => $leaveTypes,]);
    }

    public function submitLeaveRequest(Request $request)
    {
        $currentYear = date('Y');

        
    $request->validate([
        'leave_type' => 'required',
        'start_date' => [
            'required',
            'date_format:d/m/Y',
            'before_or_equal:31/12/'.$currentYear,
            'after_or_equal:01/01/'.$currentYear,
        ],
        'end_date' => [
            'required',
            'date_format:d/m/Y',
            'before_or_equal:31/12/'.$currentYear,
            'after_or_equal:start_date',
        ],
        'reason' => 'required|string|max:500',
    ], [
        'start_date.before_or_equal' => 'The start date must be within the current year ('.$currentYear.').',
        'start_date.after_or_equal' => 'The start date must be within the current year ('.$currentYear.').',
        'end_date.before_or_equal' => 'The end date must be within the current year ('.$currentYear.').',
        'end_date.after_or_equal' => 'The end date must be after or equal to the start date.',
    ]);

        $user = Auth::user();
        $employeeId = $request->employee_id ?: $user->id;
        $startDate = \DateTime::createFromFormat('d/m/Y', $request->start_date);
        $endDate = \DateTime::createFromFormat('d/m/Y', $request->end_date);

        if ($startDate === false || $endDate === false) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');
   

        $existingRequest = LeaveRequest::where('user_id', $employeeId)
        ->where('answer', 'pending') 
        ->where(function($query) use ($startDateFormatted, $endDateFormatted) {
            $query->whereBetween('start_date', [$startDateFormatted, $endDateFormatted])
                ->orWhereBetween('end_date', [$startDateFormatted, $endDateFormatted])
                ->orWhere(function($query) use ($startDateFormatted, $endDateFormatted) {
                    $query->where('start_date', '<=', $startDateFormatted)
                            ->where('end_date', '>=', $endDateFormatted);
                });
        })
        ->first();

    if ($existingRequest) {
        return redirect()->back()->withErrors(['error' => 'You have already submitted a leave request that overlaps with these dates.']);
    }

        try {
            $leaveRequest = new LeaveRequest();
            $leaveRequest->user_id = $employeeId;
            $leaveRequest->leave_type = $request->leave_type;
            $leaveRequest->start_date = $startDate;
            $leaveRequest->end_date = $endDate;
            $leaveRequest->reason = $request->reason;
            $leaveRequest->answer = 'pending';
            $leaveRequest->save();

            session()->flash('status', 'Your leave request has been submitted successfully and is pending approval.');

            if ($user->role === 'admin') {
                return redirect()->back();
            } else {
                return redirect()->back();
            } 
            } catch (\Exception $e) {
            Log::error('Error saving leave request: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to save leave request.']);
            }
    }
    public function approveDenyRequests()
        {
        
            $leaveRequests = DB::table('leave_requests as lr')
                ->join('users as u', 'lr.user_id', '=', 'u.id')
                ->join('leave_types as lt', 'lr.leave_type', '=', 'lt.id') 
                ->select('lr.*', 'u.name', 'u.surname', 'lt.name as leave_type_name') 
                ->where('lr.answer', 'pending')
                ->orderByDesc('lr.id')
                ->get();
        
            return view('admin.approve-deny-requests', compact('leaveRequests'));
        }
    
    public function processLeaveRequest(Request $request)
        {
            $request->validate([
                'request_id' => 'required',
                'action' => 'required|string|in:approve,deny',
                'response' => 'required|string|max:255',
            ]);
        
            $leaveRequest = LeaveRequest::where('id', $request->request_id)->firstOrFail();
            $leaveRequest->answer = $request->action === 'approve' ? 'approved' : 'denied';
            $leaveRequest->response_message = $request->response;
            $leaveRequest->save();

            $user = $leaveRequest->user;
            $email = $user->email;
            $fullName = $user->name.' '.$user->surname;
            $leaveType = $leaveRequest->type->name;
            $startDate = $leaveRequest->start_date;
            $endDate = $leaveRequest->end_date;

            $startDate = Carbon::parse($leaveRequest->start_date)->format('Y-m-d');
            $endDate = Carbon ::parse($leaveRequest->end_date)->format('Y-m-d');
        
            $subject = 'Leave Request ' . ucfirst($leaveRequest->answer);
            $body = "
            <html>
                <head>
                    <style>
                        .email-container {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                        }
                        .email-header {
                            background-color: #f2f2f2;
                            padding: 10px;
                            text-align: center;
                        }
                        .email-body {
                            padding: 20px;
                        }
                        .email-footer {
                            background-color: #f2f2f2;
                            padding: 10px;
                            text-align: center;
                        }
                        .email-title {
                            color: #444;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='email-header'>
                            <h2 class='email-title'>Leave Request {$leaveRequest->answer}</h2>
                        </div>
                        <div class='email-body'>
                            <p>Dear {$fullName},</p>
                            <p>Your leave request for <strong>{$leaveType}</strong> has been <strong>{$leaveRequest->answer}</strong>.</p>
                            <p><strong>Start Date:</strong> {$startDate}</p>
                            <p><strong>End Date:</strong> {$endDate}</p>
                            <p><strong>Response:</strong> {$leaveRequest->response_message}</p>
                        </div>
                        <div class='email-footer'>
                            <p>This is an automated message. Please do not reply.</p>
                        </div>
                    </div>
                </body>
            </html>
            ";
        
            Mail::to($email)->send(new \App\Mail\GenericMail($subject, $body));
        
            return redirect()->route('admin.approve-deny-requests')->with('success', 'Leave request has been ' . $leaveRequest->answer . '.');
        }
        
    
    public function viewLeaveReports(Request $request)
        {
            $leaveTypes = LeaveType::all();

            $query = LeaveRequest::with('user', 'type');

            if ($request->filled('user')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->input('user') . '%')
                    ->orWhere('surname', 'like', '%' . $request->input('user') . '%');
                });
            }

            if ($request->filled('leave_type')) {
                $query->where('leave_type', $request->input('leave_type'));
            }
            
            $requestsGrouped = $query->get()
        ->groupBy(function ($leave) {
            return $leave->user->name . ' ' . $leave->user->surname;
        })
        ->map(function ($leaves) use ($leaveTypes) {
            $totalDays = $leaveTypes->mapWithKeys(function ($type) {
                return [$type->name => ['approved' => 0]];
            })->toArray(); 

            foreach ($leaves as $leave) {
                $days = $leave->start_date->diffInDays($leave->end_date) + 1;
                $leaveTypeName = $leave->type->name; 

                if ($leave->answer === 'approved') {
                    if (array_key_exists($leaveTypeName, $totalDays)) {
                        $totalDays[$leaveTypeName]['approved'] += $days;
                    }
                }
            }

            return [
                'requests' => $leaves->map(function ($leave) {
                    return [
                        'leave_type' => $leave->type->name,
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'requested_days' => $leave->start_date->diffInDays($leave->end_date) + 1,
                        'answer' => $leave->answer,
                    ];
                })->toArray(),
                'total_days' => $totalDays, 
            ];
        })
        ->sortKeys()
        ->toArray();


            return view('admin.view-leave-reports', compact('requestsGrouped', 'leaveTypes'));
        }
}
