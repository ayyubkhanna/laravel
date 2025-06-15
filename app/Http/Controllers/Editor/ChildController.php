<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ChildController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('children-create')){

                $validator = Validator::make($request->all(), [
                    'people_id' => 'required|integer',
                    'kia' => 'required|integer',
                    'nik' => 'required|integer',
                    'orang_tua' => 'required',
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'validation failed', $validator->getMessageBag(), 422);
                }

                $person = Person::findOrFail($request->people_id);

                $children = Child::create([
                    'people_id' => $person->id,
                    'kia' => $request->kia,
                    'nik' => $request->nik,
                    'orang_tua' => $request->orang_tua
                ]);

                return $this->httpResponse(true, 'created success', $children, 201);

            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'Data not found', $th->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('children-update')){

                $children = Child::findOrFail($id);

                $validator = Validator::make($request->all(), [
                    'kia' => 'required|integer',
                    'name' => 'required',
                    'nik' => 'required|integer',
                    'alamat' => 'required',
                    'orang_tua' => 'required',
                    'posyandu_id' => 'required',
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'validation failed', $validator->getMessageBag(), 422);
                }

                $children->update($request->all());

                return $this->httpResponse(true, 'updated success', $children, 200);

            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'Data not found', $th->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if(request()->user()->hasRole('editor') && request()->user()->isAbleTo('children-delete')){
                $child = Child::findOrFail($id);

                $child->delete();

                return $this->httpResponse(true, 'deleted success', [], 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'Data not found', $th->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
    }
}
