<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
}
