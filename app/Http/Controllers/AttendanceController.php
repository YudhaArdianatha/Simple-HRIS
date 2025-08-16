<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendance = Attendance::with('employee')->get();
        return response()->json($attendance);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'check_in' => 'required|date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s|after:check_in',
        ]);

        // Cek attendance yang belum ditutup (check_out null) untuk hari yang sama
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->whereDate('check_in', date('Y-m-d', strtotime($request->check_in)))
            ->whereNull('check_out')
            ->first();

        if($existingAttendance){
            return response()->json([
                'error' => 'Employee already has an open attendance record for today'
            ], 422);
        }

        $data = $request->only(['employee_id', 'check_in', 'check_out']);

        $attendance = Attendance::create($data);


        return response()->json([
            'message' => 'Attendance record created successfully',
            'data' => $attendance
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return response()->json($attendance->load('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        try {
        $request->validate([
            'check_in' => 'date_format:Y-m-d H:i:s',
            'check_out' => 'nullable|date_format:Y-m-d H:i:s|after:check_in',
        ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $data = $request->only(['check_in', 'check_out']);

        if (isset($data['check_out']) && $attendance->check_out !== null) {
            return response()->json([
                'error' => 'Attendance record already has a check-out time'
            ], 422);
        }

        $attendance->update($data);

        return response()->json([
            'message' => 'Attendance record updated successfully',
            'data' => $attendance
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Attendance record deleted successfully'], 204);
    }

    public function restore($id)
    {
        $attendance = Attendance::withTrashed()->find($id);

        if (!$attendance) {
            return response()->json(['message' => 'Attendance record not found'], 404);
        }

        $attendance->restore();

        return response()->json([
            'message' => 'Attendance record restored successfully',
            'data' => $attendance
        ]);
    }
}
