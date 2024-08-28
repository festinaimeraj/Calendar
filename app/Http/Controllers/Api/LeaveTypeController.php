<?php

namespace App\Http\Controllers\Api;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        if(!$leaveType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Leave type not found',
            ], 404);
        }
        return response()->json($leaveType);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_days' => 'nullable|integer|min:0',
        ]);

        $leaveType = LeaveType::create([
            'name' => $request->name,
            'max_days' => isset($validated['max_days']) ? $validated['max_days'] : null, // Use null if not provi
        ]);
        return response()->json([
            'leave_type' => $leaveType,
            'status' => 'success',
            'message' => 'Leave type created successfully',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::find($id);

        if(!$leaveType) {
            return response()->json([
            'success' => false,
            'message' => 'Leave type not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'max_days' => 'nullable|integer|min:0',
        ]);

        $leaveType->update([
            'name' => $request->name,
            'max_days' => isset($validated['max_days']) ? $validated['max_days'] : $leaveType->max_days, // Preserve existing value if not provided
        ]);

        return response()->json([
            'leave_type' => $leaveType,
            'success' => true,
            'message' => 'Leave type updated successfully',
        ]);
    }

    public function destroy($id)
    {
       $leaveType = LeaveType::find($id);

       if(!$leaveType) {
           return response()->json([
            'success' => false,
            'message' => 'Leave type not found'], 404);
       }

       $leaveType->delete();

       return response()->json([
        'success' => true,
        'message' => 'Leave type deleted successfully']);
    }
}
