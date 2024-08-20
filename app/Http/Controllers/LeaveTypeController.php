<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return response()->json($leaveTypes);
    }

    public function show($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return response()->json($leaveType);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $leaveType = LeaveType::create($request->all());
        return response()->json($leaveType, 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $leaveType = LeaveType::findOrFail($id);
        $leaveType->update($request->all());
        return response()->json($leaveType);
    }

    public function destroy($id)
    {
        LeaveType::destroy($id);
        return response()->json(null, 204);
    }
}
