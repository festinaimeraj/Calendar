<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use App\Models\LeaveRequest;
// use Illuminate\Support\Facades\Mail;
// use App\Mail\GenericMail;

// class AdminReportController extends Controller
// {
//     public function index()
//     {
//         return view('admin.index');
//     }

//     public function calendar()
//     {
//         return view('admin.calendar');
//     }

//     public function employees()
//     {
//         $employees = User::where('role', 'employee')
//             ->leftJoin('leave_requests', 'users.id', '=', 'leave_requests.user_id')
//             ->select('users.id', 'users.username', 'users.name', 'users.surname', 'users.email', 
//                 DB::raw('COALESCE(SUM(DATEDIFF(leave_requests.end_date, leave_requests.start_date) + 1), 0) AS total_days_used'))
//             ->where('leave_requests.answer', 'approved')
//             ->groupBy('users.id', 'users.username', 'users.name', 'users.surname', 'users.email')
//             ->get();

//         return view('admin.employees', compact('employees'));
//     }

//     public function addEmployee()
//     {
//         return view('admin.addEmployee');
//     }

//     public function storeEmployee(Request $request)
//     {
//         $request->validate([
//             'username' => 'required|string|max:255',
//             'name' => 'required|string|max:255',
//             'surname' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8|confirmed',
//         ]);

//         User::create([
//             'username' => $request->username,
//             'name' => $request->name,
//             'surname' => $request->surname,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'role' => 'employee',
//         ]);

//         return redirect()->route('admin.employees')->with('success', 'Employee added successfully.');
//     }

//     public function editEmployee($id)
//     {
//         $employee = User::findOrFail($id);
//         return view('admin.editEmployee', compact('employee'));
//     }

//     public function updateEmployee(Request $request, $id)
//     {
//         $request->validate([
//             'username' => 'required|string|max:255',
//             'name' => 'required|string|max:255',
//             'surname' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users,email,'.$id,
//         ]);

//         $employee = User::findOrFail($id);
//         $employee->update([
//             'username' => $request->username,
//             'name' => $request->name,
//             'surname' => $request->surname,
//             'email' => $request->email,
//         ]);

//         return redirect()->route('admin.employees')->with('success', 'Employee updated successfully.');
//     }

//     public function admins()
//     {
//         $admins = User::where('role', 'admin')
//             ->leftJoin('leave_requests', 'users.id', '=', 'leave_requests.user_id')
//             ->select('users.id', 'users.username', 'users.name', 'users.surname', 'users.email', 
//                 DB::raw('COALESCE(SUM(DATEDIFF(leave_requests.end_date, leave_requests.start_date) + 1), 0) AS total_days_used'))
//             ->where('leave_requests.answer', 'approved')
//             ->groupBy('users.id', 'users.username', 'users.name', 'users.surname', 'users.email')
//             ->get();

//         return view('admin.admins', compact('admins'));
//     }

//     public function addAdmin()
//     {
//         return view('admin.addAdmin');
//     }

//     public function storeAdmin(Request $request)
//     {
//         $request->validate([
//             'username' => 'required|string|max:255',
//             'name' => 'required|string|max:255',
//             'surname' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8|confirmed',
//         ]);

//         User::create([
//             'username' => $request->username,
//             'name' => $request->name,
//             'surname' => $request->surname,
//             'email' => $request->email,
//             'password' => Hash::make($request->password),
//             'role' => 'admin',
//         ]);

//         return redirect()->route('admin.admins')->with('success', 'Admin added successfully.');
//     }

//     public function editAdmin($id)
//     {
//         $admin = User::findOrFail($id);
//         return view('admin.editAdmin', compact('admin'));
//     }

//     public function updateAdmin(Request $request, $id)
//     {
//         $request->validate([
//             'username' => 'required|string|max:255|unique:users,username,'.$id,
//             'name' => 'required|string|max:255',
//             'surname' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users,email,'.$id,
//         ]);

//         $admin = User::findOrFail($id);
//         $admin->update([
//             'username' => $request->username,
//             'name' => $request->name,
//             'surname' => $request->surname,
//             'email' => $request->email,
//         ]);

//         return redirect()->route('admin.admins')->with('success', 'Admin updated successfully.');
//     }

//     public function requestLeave()
//     {
//         return view('admin.request_leave');
//     }

//     public function approveDenyRequests()
//     {
//         $leaveRequests = DB::table('leave_requests as lr')
//             ->join('users as u', 'lr.user_id', '=', 'u.id')
//             ->select('lr.*', 'u.username')
//             ->where('lr.answer', 'pending')
//             ->orderByDesc('lr.requestId')
//             ->get();

//         return view('admin.approve-deny-requests', compact('leaveRequests'));
//     }

//     public function processLeaveRequest(Request $request)
//     {
//         $request->validate([
//             'request_id' => 'required|integer|exists:leave_requests,requestId',
//             'action' => 'required|string|in:approve,deny',
//             'response' => 'required|string|max:255',
//         ]);

//         $leaveRequest = LeaveRequest::findOrFail($request->request_id);
//         $leaveRequest->answer = $request->action === 'approve' ? 'approved' : 'denied';
//         $leaveRequest->response_message = $request->response;
//         $leaveRequest->save();

//         $user = $leaveRequest->user;
//         $email = $user->email;
//         $username = $user->username;
//         $leaveType = $leaveRequest->leave_type;
//         $startDate = $leaveRequest->start_date;
//         $endDate = $leaveRequest->end_date;

//         $subject = 'Leave Request ' . ucfirst($leaveRequest->answer);
//         $body = view('emails.leave_response', compact('username', 'leaveType', 'startDate', 'endDate', 'leaveRequest'))->render();

//         Mail::to($email)->send(new \App\Mail\GenericMail($subject, $body));

//         return redirect()->route('admin.approveDenyRequests')->with('success', 'Leave request has been ' . $leaveRequest->answer . '.');
//     }

//     public function viewLeaveReports()
//     {
//         $requestsGrouped = LeaveRequest::select('leave_type', DB::raw('count(*) as total_requests'))
//         ->join('leave_types as lt', 'leave_requests.leave_type', '=', 'lt.id')
//             ->groupBy('leave_type')
//             ->get();


//         return view('admin.view-leave-reports', compact('requestsGrouped'));
//     }
// }
