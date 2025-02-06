<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Cache::remember('users', now()->addMinutes(5), function() {
            return User::paginate(10);
        });

        $roles = [];

        foreach ($user as $value) {
            $roles[] = $value->roles;
        }

        // dd($roles);

        if($user) {
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => $user,
            ], 200)->header('Cache-Control', 'public, max-age=3600');;
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => ['required'],
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
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

         $role = Role::where('name', $request->role)->firstOrFail();

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
        $user = User::find($id);

        if($user) {
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => $user
            ], 404);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => ['nullable'],
            'name' => ['nullable', 'max:255'],
            'email' => ['nullable', 'email'],
            'password' => ['nullable', 'confirmed'],
         ]);

         if($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'data' => $validator->getMessageBag()]);
         }

         $user = User::find($id);

         $user->update($request->all());

         $roles = $user->roles;


         foreach ($roles as $role) {
             $user->removeRole($role);
         }

         $role = Role::where('name', $request->role)->firstOrFail();

         $user->addRole($role->id);

         return response()->json([
            'status' => true,
            'message' => 'Created Successfully',
            'data' => $user
         ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        $roles = $user->roles;

        foreach ($roles as $role) {
            $user->removeRole($role);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted Successfull'
        ], 200);
    }
}
