<?php

namespace App\Http\Controllers\Editor\Pregnant;

use App\Http\Controllers\Controller;
use App\Models\Pregnant;
use App\Models\PrenetalCheckup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrenetalCheckupController extends Controller
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
            if($request->user()->hasRole('editor') || $request->user()->isAbleTo('create-prenetal-checkup')) {

                // Validasi data
                $validator = Validator::make($request->all(), [
                    'pregnantId' => 'required|exists:pregnants,id',
                    'date' => 'required|date',
                    'bodyWeight' => 'required|integer',
                    'bodyHeight' => 'required|integer',
                    'upperArm' => 'required|integer',
                    'abdominal' => 'required|integer',
                    'bloodPressure' => 'required|integer',
                    'immunization' => 'nullable|string',
                    'advice' => 'nullable|string'
                ]);

                // handel validasi error
                if($validator->fails()) {
                    return $this->httpResponseError(false, 'validation failed', $validator->errors(), 422);
                }
                

                $pregnant = Pregnant::find($request->pregnantId);
                
                if(!$pregnant) {
                    return $this->httpResponseError(false, 'pregnant not found', [], 404);
                }

                // buat data di prenental checkups
                $data = $pregnant->prenatalCheckups()->create([
                    'date' => $request->date,
                    'bodyWeight' => $request->bodyWeight,
                    'bodyHeight' => $request->bodyHeight,
                    'upperArm' => $request->upperArm,
                    'abdominal' => $request->abdominal,
                    'bloodPressure' => $request->bloodPressure,
                    'immunization' => $request->immunization,
                    'advice' => $request->advice
                ]);

                return $this->httpResponse(true, 'created success', $data, 201);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            if($request->user()->hasRole('editor') || $request->user()->isAbleTo('create-prenetal-checkup')) {

                // cek pregnant id di prenetal checkup
                $data = PrenetalCheckup::where('pregnantId', $id)->get();

                if(!$data) {
                    return $this->httpResponseError(false, 'checkup pregantn not found', [], 404);
                }

                return $this->httpResponse(true, 'success', $data, 200);

            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'you dont have access', $th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if($request->user()->hasRole('editor') || $request->user()->isAbleTo('update-prenetal-checkup')) {

                // Validasi data
                $validator = Validator::make($request->all(), [
                    'date' => 'required|date',
                    'bodyWeight' => 'required|integer',
                    'bodyHeight' => 'required|integer',
                    'upperArm' => 'required|integer',
                    'abdominal' => 'required|integer',
                    'bloodPressure' => 'required|integer',
                    'immunization' => 'nullable|string',
                    'advice' => 'nullable|string'
                ]);

                // handel validasi error
                if($validator->fails()) {
                    return $this->httpResponseError(false, 'validation failed', $validator->errors(), 422);
                }

                $prenentalCheckup =  PrenetalCheckup::find($id);

                // jika checkup tidak ditemukan
                if(!$prenentalCheckup) {
                    return $this->httpResponseError(false, 'checkup not found', [], 404);
                }

                // buat data di prenental checkups
                $data = $prenentalCheckup->update([
                    'date' => $request->date,
                    'bodyWeight' => $request->bodyWeight,
                    'bodyHeight' => $request->bodyHeight,
                    'upperArm' => $request->upperArm,
                    'abdominal' => $request->abdominal,
                    'bloodPressure' => $request->bloodPressure,
                    'immunization' => $request->immunization,
                    'advice' => $request->advice
                ]);

                return $this->httpResponse(true, 'updated success', $data, 201);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            
            // cek role dari user
            if(request()->user()->hasRole(['editor', 'admin']) || request()->user()->isAbleTo('delete-prenetal-checkup')) {
                
                $prenetalCheckup = PrenetalCheckup::find($id);

                // cek jika checkup tidak ada
                if(!$prenetalCheckup) {
                    return $this->httpResponseError(false, 'checkup not found', [], 404);
                }

                $prenetalCheckup->delete();

                return $this->httpResponse(true, 'deleted success', [], 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(),500);
        }
    }
}
