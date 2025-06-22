<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Person;
use App\Models\Pregnant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ChildController extends Controller
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
        try {

            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('children-create')){

                $validator = Validator::make($request->all(), [
                    'peopleId' => 'required|integer|unique:children,peopleId|exists:people,id',
                    'motherId' => 'required|integer|exists:pregnants,id',
                    'numberKia' => 'nullable|integer',
                    'gender' => 'required|in:male,female'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'validation failed', $validator->getMessageBag(), 422);
                }

                $person = Person::where('id',$request->peopleId)->first();
                
                $mother = Pregnant::findOrFail($request->motherId);

                if($person->pregnant->isNotEmpty()) {
                    return $this->httpResponseError(false, 'data ini sudah mendapat status hamil aktif', "BAD REQUEST", 400);
                }

                $children = $person->child()->create([
                    'motherId' => $mother->id,
                    'numberKia' => $request->numberKia,
                    'gender' => $request->gender
                ]);

                Cache::tags(['children'])->flush();

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
                    'peopleId' => 'required|integer',
                    'motherId' => 'required|integer',
                    'numberKia' => 'nullable|integer',
                    'gender' => 'required|in:male,female'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'validation failed', $validator->getMessageBag(), 422);
                }

                dd('tes');

                $children->update($request->only(['peopleId', 'motherId', 'kia', 'gender']));

                Cache::tags(['children'])->flush();

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
       //
    }
}
