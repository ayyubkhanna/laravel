<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Cache::remember('roles', now()->addMinutes(5), function() {
            return Role::all();
        });

        if($roles) {
            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => $roles,
                // 'rolePermissions' => $rolePermissions
            ], 200);
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
            'name' => ['required'],
            'display_name' => ['required'],
            'description' => ['required'],
            'permission_id' => ['required']
        ]);


        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'data' => $validator->getMessageBag()], 422);
         }

         $roles = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
         ]);

         $roles->givePermissions($request->permission_id);

         Cache::forget('roles');

         return response()->json([
            'status' => true,
            'message' => 'Created Role Success',
            'data' => $roles
         ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

            $roles = Role::find($id);

            $roles->permissions;

            if($roles) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $roles
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Role not found',
                    'data' => $roles
                ], 404);
            }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'display_name' => ['required'],
            'description' => ['required'],
            'permission_id' => ['required']
        ]);


        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'data' => $validator->getMessageBag()], 422);
         }

         $roles = Role::find($id);

         if(!$roles) {
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
         }

         $roles->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
         ]);

         $roles->syncPermissions($request->permission_id);

         Cache::forget('roles');

         return response()->json([
            'status' => true,
            'message' => 'Updated Role Success',
            'data' => $roles
         ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if(!$role){
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404);
        }

        $role->delete();

        Cache::forget('roles');

        return response()->json([
            'status' => true,
            'message' => 'Deleted Success'
        ], 200);
    }
}
