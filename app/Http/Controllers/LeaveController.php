<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = Leave::with('employee')->get();
        return response()->json($leaves);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'reason' => 'required|string|max:255',
            ]);
    
            $leave = Leave::create($request->only(['employee_id', 'start_date', 'end_date', 'reason']));
    
            return response()->json([
                'message' => 'Leave request created successfully',
                'data' => $leave
            ], 201);
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        catch (Exception $e) {
            return response()->json(['error' => 'Failed to create leave request'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $leave = Leave::findOrFail($id);
        return response()->json($leave->load('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $leave = Leave::find($id);

            if (!$leave) {
                return response()->json([
                    'error' => 'Leave record not found'
                ], 404);
            }

            $request->validate([
                'start_date' => 'sometimes|date',
                'end_date'   => 'sometimes|date|after:start_date',
                'reason'     => 'sometimes|string|max:255',
                'status'     => 'sometimes|in:pending,approved,rejected',
            ]);

            $leave->update($request->only(['start_date', 'end_date', 'reason', 'status']));

            return response()->json([
                'message' => 'Leave request updated successfully',
                'data' => $leave
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $leave = Leave::find($id);

        if (!$leave) {
            return response()->json(['message' => 'Leave request not found'], 404);
        }

        $leave->delete();
        return response()->json(['message' => 'Leave request deleted successfully'], 204);
    }

    public function restore($id)
    {
        $leave = Leave::withTrashed()->find($id);

        if (!$leave) {
            return response()->json(['message' => 'Leave request not found'], 404);
        }

        $leave->restore();

        return response()->json([
            'message' => 'Leave request restored successfully',
            'data' => $leave
        ]);
    }
}
