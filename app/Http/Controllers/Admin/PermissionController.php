<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $permission = Cache::remember('permissions', now()->addMinutes(5), function () {
            return Permission::all();
        });

        if ($permission->isNotEmpty()) {
            return $this->httpResponse(true, 'Success', $permission, 200);
        } else {
            return $this->httpResponse(false, 'Data not found', [], 404);
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
        ]);

        if($validator->fails()) {
            return $this->httpResponse(false, 'Validation Failed', $validator->getMessageBag(), 422);
         }

         $permission = Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
         ]);

         Cache::forget('permissions');

         return $this->httpResponse(true, 'Created Permission', $permission, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permission = Permission::find($id);

        if($permission){
            return $this->httpResponse(true, 'Success', $permission, 200);
        } else {
            return $this->httpResponse(false, 'Data not found', '', 404);
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
        ]);

        if($validator->fails()) {
            return $this->httpResponse(false, 'Validation Failed', $validator->getMessageBag(), 422);
         }

         $permission = Permission::find($id);

         if(!$permission) {
            return $this->httpResponse(false, 'Not found', '', 404);
         }

         $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
         ]);


         Cache::forget('permissions');

         return $this->httpResponse(true, 'Updated Successfully', $permission, 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);

        if(!$permission) {
            return $this->httpResponse(false, 'Not found', '', 404);
        }

        $permission->delete();

        Cache::forget('permissions');

        return $this->httpResponse(true, 'Deleted Success', '', 200);
    }
}
