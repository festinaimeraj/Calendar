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
            'password' => 'required|string|min:8|confirmed',
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

    public function approveDenyRequests()
    {
        $leaveRequests = DB::table('leave_requests as lr')
            ->join('users as u', 'lr.user_id', '=', 'u.id')
            ->select('lr.*', 'u.name', 'u.surname')
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
    
    public function viewLeaveReports()
    {
        $requestsGrouped = LeaveRequest::with('user') // Eager load the related user
        ->get()
        ->groupBy(function ($leave) {
            return $leave->user->name.' '.$leave->user->surname; // Group by the user's username
        })
        ->map(function ($leaves, $username) {
            return $leaves->map(function ($leave) {
                return [
                    'leave_type' => $leave->type->name,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'requested_days' => $leave->start_date->diffInDays($leave->end_date) + 1, // Calculate requested days
                    'answer' => $leave->answer, // Assuming there is a 'status' column
                    'action' => $leave->action, // Assuming there is an 'action' column
                ];
            })->toArray();
        })
        ->toArray();
  
        return view('admin.view-leave-reports', compact('requestsGrouped'));
    }
}
