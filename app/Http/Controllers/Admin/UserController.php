<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => ['required'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
         ]);

         if($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'data' => $validator->getMessageBag()]);
         }

         $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
         ]);

         $role = Role::where('id', $request->role)->firstOrFail();

         $user->addRole($role->id);

         return response()->json([
            'status' => true,
            'message' => 'Created Successfully',
            'data' => $user
         ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
