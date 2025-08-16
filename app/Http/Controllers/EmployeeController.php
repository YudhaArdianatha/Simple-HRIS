<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Leave;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = Employee::all();
        return response()->json($employee);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'position' => 'required|string|max:255',
            ]);
    
            
    
            $data = $request->all();
            $data['slug'] = Str::slug($request->name, '-');
    
            $employee = Employee::create($data);
            
            return response()->json($employee, 201);
        }
        catch(ValidationException){
            if ($request->has('email')) {
                return response()->json(['message' => 'Email already exists'], 422);
            }
            return response()->json(['message' => 'Validation failed'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json($employee, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'position' => 'required|string|max:255',
            ]);

            $data = $request->all();
            $data['slug'] = Str::slug($request->name, '-');

            $employee->update($data);
            return response()->json($employee, 200);
        }
        catch(ValidationException $e) {
            if ($request->has('email')) {
                return response()->json(['message' => 'Email already exists'], 422);
            }
            return response()->json(['message' => 'Validation failed'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $employee = Employee::withTrashed()->find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->restore();
        return response()->json($employee, 200);
    }

    public function getEmployeeAttendances($employeeId)
    {
        $attendance = Attendance::where('employee_id', $employeeId)->get();

        if ($attendance->isEmpty()) {
            return response()->json(['message' => 'No attendance records found for this employee'], 404);
        }

        return response()->json($attendance, 200);
    }

    public function addEmployeeAttendances($employeeId, Request $request)
    {
        try {
            $request->validate([
                'check_in' => 'required|date_format:Y-m-d H:i:s',
                'check_out' => 'nullable|date_format:Y-m-d H:i:s|after:check_in',
            ]);
    
            $employee = Employee::find($employeeId);
    
            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }
    
            $existingAttendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('check_in', date('Y-m-d', strtotime($request->check_in)))
                ->whereNull('check_out')
                ->first();
    
            if ($existingAttendance) {
                return response()->json(['message' => 'Attendance for this employee already exists for today'], 422);
            }
    
            $attendance = Attendance::create([
                'employee_id' => $employeeId,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
            ]);
    
            return response()->json([
                'message' => 'Attendance record created successfully',
                'data' => $attendance
            ], 201);
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        catch (Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }

    public function getEmployeeLeaves($employeeId)
    {
        $leaves = Leave::where('employee_id', $employeeId)->get();

        if ($leaves->isEmpty()) {
            return response()->json(['message' => 'No leave records found for this employee'], 404);
        }

        return response()->json($leaves, 200);
    }

    public function addEmployeeLeaves($employeeId, Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'reason' => 'required|string|max:255',
            ]);

            $employee = Employee::find($employeeId);

            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $leave = Leave::create([
                'employee_id' => $employeeId,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
            ]);

            return response()->json([
                'message' => 'Leave request created successfully',
                'data' => $leave
            ], 201);
        }
        catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        catch (Exception $e) {
            return response()->json(['message' => 'An error occurred'], 500);
        }
    }
}
