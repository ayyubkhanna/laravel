<?php

namespace App\Http\Controllers\Editor\Pregnant;

use App\Http\Controllers\Controller;
use App\Models\Pregnant;
use App\Models\PregnantCheckup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CheckupController extends Controller
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
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('checkup-pregnant-create')) {
                $validator = Validator::make($request->all(), [
                    'pregnant_id' => 'required|exists:pregnants,id',
                    'date' => 'required|date',
                    'result' => 'required',
                    'notes' => 'nullable',
                    'medicine' => 'nullable'
                ]);

                if($validator->fails()){
                    return $this->httpResponse(false, 'Failed validation', $validator->getMessageBag(), 422);
                }

                $pregnant = Pregnant::findOrFail($request->pregnant_id);
                
                $checkup = PregnantCheckup::create([
                    'pregnant_id' => $pregnant->id,
                    'date' => $request->date,
                    'result' => $request->result,
                    'notes' => $request->notes,
                    'medicine' => $request->medicine
                ]);
                
                Cache::tags(['pregnant_checkups'])->flush();

                return $this->httpResponse(true, 'Created success', $checkup, 201);
            } else {
                return $this->httpResponse(false, 'You dont have access', '', 403);
            }
        } catch (ModelNotFoundException $e) {
            return $this->httpResponseError(false, 'Data not found', $e->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(), 500);
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
            if(request()->user()->hasRole('editor') || request()->user()->isAble('checkup-pregnant-update')) {

                $checkup = PregnantCheckup::findOrFail($id);

                $validator = Validator::make($request->all(), [
                    'date' => 'nullable|date',
                    'result' => 'nullable',
                    'notes' => 'nullable',
                    'medicine' => 'nullable'
                ]);

                if($validator->fails()){
                    return $this->httpResponse(false, 'Failed validation', $validator->getMessageBag(), 422);
                }

                $checkup->update($request->only(['date', 'result', 'notes', 'medicine']));

                Cache::tags(['pregnant_checkups'])->flush();

                return $this->httpResponse(true, 'Updated Success', $checkup, 200);

            } else {
                return $this->httpResponseError(false, 'You dont have access', '', 403);
            }
            
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('checkup-pregnant-delete')) {

                $checkup = PregnantCheckup::findOrFail($id);
                
                $checkup->delete();

                Cache::tags(['pregnant_checkups'])->flush();

                return $this->httpResponse(true, 'deleted success', '', 200);
            } else {
                return $this->httpResponseError(false, 'You dont have access', '', 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, $th->getMessage(), '', 500);
        }
    }
}
