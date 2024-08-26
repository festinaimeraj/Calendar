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

 
    public function showEmployee()
    {
        // Fetch the employee details
        $employees = User::where('role', 'employee')->get();

        // Fetch total days used by the employee
        $totalDaysUsed = LeaveRequest::where('user_id', 1)
            ->where('answer', 'approved')
            ->selectRaw('SUM(DATEDIFF(end_date, start_date) + 1) as total_days_used')
            ->pluck('total_days_used')
            ->first();

        return view('admin.employees', compact('employees', 'totalDaysUsed'));
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
        // Fetch the employee details
        $admins = User::where('role', 'admin')->get();
        return view('admin.admins', compact('admins'));
    }

    // Method to add an admin
    public function addAdmin()
    {
        return view('admin.addAdmin');
    }

    // Method to store an admin
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

    // Method to edit an admin
    public function editAdmin($id)
    {
        $admin = User::findOrFail($id);
        return view('admin.editAdmin', compact('admin'));
    }

    // Method to update an admin
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
        $leaveTypes = LeaveType::all();

        return view('admin.request_leave', compact('leaveTypes'));
    }

    public function submitLeaveRequest(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date_format:d/m/Y',
        'end_date' => 'required|date_format:d/m/Y',
            'reason' => 'required|string|max:500',
        ]);
        

        $user = Auth::user();
        $startDate = \DateTime::createFromFormat('d/m/Y', $request->start_date);
        $endDate = \DateTime::createFromFormat('d/m/Y', $request->end_date);

        if ($startDate === false || $endDate === false) {
            // Handle the error if the date creation failed
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Format the date to 'Y-m-d' format
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');


        try {
            $leaveRequest = new LeaveRequest();
            $leaveRequest->user_id =  Auth::user()->id;
            $leaveRequest->leave_type = $request->leave_type;
            $leaveRequest->start_date = $startDate;
            $leaveRequest->end_date = $endDate;
            $leaveRequest->reason = $request->reason;
            $leaveRequest->answer = 'pending';
            $leaveRequest->save();

            // Set a success message in the session
            session()->flash('status', 'Your leave request has been submitted successfully and is pending approval.');

            // Redirect based on the role
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
            ->join('leave_types as lt', 'lr.leave_type', '=', 'lt.id') // Join with leave_types table
            ->select('lr.*', 'u.name', 'u.surname', 'lt.name as leave_type_name') // Select leave type name
            ->where('lr.answer', 'pending')
            ->orderByDesc('lr.id')
            ->get();
    
        return view('admin.approve-deny-requests', compact('leaveRequests'));
    }
    
    public function processLeaveRequest(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'request_id' => 'required',
            'action' => 'required|string|in:approve,deny',
            'response' => 'required|string|max:255',
        ]);
    
        // Find the leave request and update it
        $leaveRequest = LeaveRequest::where('id', $request->request_id)->firstOrFail();
        $leaveRequest->answer = $request->action === 'approve' ? 'approved' : 'denied';
        $leaveRequest->response_message = $request->response;
        $leaveRequest->save();
    
        // Fetch user details for email
        $user = $leaveRequest->user;
        $email = $user->email;
        $username = $user->username;
        $leaveType = $leaveRequest->leave_type;
        $startDate = $leaveRequest->start_date;
        $endDate = $leaveRequest->end_date;
    
        // Prepare and send the email
        $subject = 'Leave Request ' . ucfirst($leaveRequest->answer);
        $body = view('emails.leave_response', compact('username', 'leaveType', 'startDate', 'endDate', 'leaveRequest'))->render();
    
        Mail::to($email)->send(new \App\Mail\GenericMail($subject, $body));
    
        return redirect()->route('admin.approve-deny-requests')->with('success', 'Leave request has been ' . $leaveRequest->answer . '.');
    }
    
    public function viewLeaveReports(Request $request)
    {
        // Fetch all leave types
        $leaveTypes = LeaveType::all();

        // Initialize the query builder for leave requests
    $query = LeaveRequest::with('user', 'type');

    // Apply the search by username if provided
    if ($request->filled('user')) {
        $query->whereHas('user', function($q) use ($request) {
            $q->where('name', 'like', '%' . $request->input('user') . '%')
              ->orWhere('surname', 'like', '%' . $request->input('user') . '%');
        });
    }

    // Apply the search by leave type if provided
    if ($request->filled('leave_type')) {
        $query->where('leave_type', $request->input('leave_type'));
    }
        
        // Group leave requests by user's full name and map the necessary details
        $requestsGrouped = LeaveRequest::with('user', 'type') // Eager load related user and leave type
            ->get()
            ->groupBy(function ($leave) {
                return $leave->user->name . ' ' . $leave->user->surname; // Group by user's full name
            })
            ->map(function ($leaves, $username) {
                return $leaves->map(function ($leave) {
                    return [
                        'leave_type' => $leave->type->name,
                        'start_date' => $leave->start_date,
                        'end_date' => $leave->end_date,
                        'requested_days' => $leave->start_date->diffInDays($leave->end_date) + 1,
                        'answer' => $leave->answer,
                        'action' => $leave->action,
                    ];
                })->toArray();
            })
            ->toArray();
      
        // Pass both $requestsGrouped and $leaveTypes to the view
        return view('admin.view-leave-reports', compact('requestsGrouped', 'leaveTypes'));
    }
    
}
