<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('leave_types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('leave_types.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'max_days' => 'required|integer|min:0',
        ]);

        LeaveType::create($request->all());
        return redirect()->route('admin.leave_types.index')->with('success', 'Leave Type created successfully.');
    }

   public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('leave_types.edit', compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_days' => 'required|integer|min:0',
        ]); 

        $leaveType = LeaveType::findOrFail($id);
        $leaveType->update($request->all());
        return redirect()->route('admin.leave_types.index')->with('success', 'Leave Type updated successfully.');
    }

    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->delete();
        return redirect()->route('admin.leave_types.index')->with('success', 'Leave Type deleted successfully.');
    }
    
}
