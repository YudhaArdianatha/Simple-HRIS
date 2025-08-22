<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json([
            'message' => 'List of users',
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'required|exists:roles,name',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->roles);

            return response()->json([
                'message' => 'User created successfully',
                'data' => $user->load('roles')
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User creation failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user = User::with('roles')->findOrFail($user->id);

        return response()->json([
            'message' => 'User details',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try{
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'sometimes|string|min:8|confirmed',
                'roles' => 'sometimes|exists:roles,name',
            ]);

            if ($request->has('roles')) {
                $user->syncRoles([$request->roles]);
            }

            $user = User::findOrFail($user->id);

            $user->update([
                'name'=> $request->name ?? $user->name,
                'email'=> $request->email ?? $user->email,
                'password'=> $request->password ? bcrypt($request->password) : $user->password,
            ]);

            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user->load('roles')
            ], 200);
        } catch(ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User update failed',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user = User::findOrFail($user->id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }

    public function restore(User $user)
    {
        $user = User::withTrashed()->findOrFail($user->id);
        if (!$user->trashed()) {
            return response()->json([
                'message' => 'User is not deleted',
                'data' => $user
            ], 400);
        }

        $user->restore();

        return response()->json([
            'message' => 'User restored successfully',
            'data' => $user
        ], 200);
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($user->id);
        $user->syncRole($request->roles);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'data' => $user->load('roles')
        ], 200);
    }
}