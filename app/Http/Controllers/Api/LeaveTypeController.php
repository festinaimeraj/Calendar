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
            return response()->json(['message' => 'Leave type not found'], 404);
        }
        return response()->json($leaveType);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $leaveType = LeaveType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return response()->json($leaveType, 201);
    }

    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::find($id);

        if(!$leaveType) {
            return response()->json(['message' => 'Leave type not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $leaveType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($leaveType);
    }

    public function destroy($id)
    {
       $leaveType = LeaveType::find($id);

       if(!$leaveType) {
           return response()->json(['message' => 'Leave type not found'], 404);
       }

       $leaveType->delete();

       return response()->json(['message' => 'Leave type deleted successfully']);
    }
}
