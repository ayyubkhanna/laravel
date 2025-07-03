<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Pregnant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PregnantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->user()->hasRole(['editor', 'admin']) || $request->user()->isAbleTo('pregnants-create')) {
                
                // tampilkan semua pregnant yang aktif
                $pregnant = Pregnant::with('person')->where('status', 'aktif')->paginate(10);

                return $this->httpResponse(true, 'success', $pregnant, 200);
            } else {

            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
            
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('pregnants-create')) {
                $validator = Validator::make($request->all(), [
                    'peopleId' => 'required|integer|exists:people,id',
                    'pregnancyStartDate' => 'required|date',
                    'estimatedDueDate' => 'required|date',
                    'husbandName' => 'required|string',
                    'actualDeliveryDate' => 'nullable|date',
                    'status' => 'required|in:aktif,melahirkan,selesai'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                }

                $person = Person::where('id',$request->peopleId)->first();
                
                if($person->child) {
                    return $this->httpResponseError(false, 'person already status child', "BAD REQUEST", 400);
                }
                
                // jika pregnant sudah memiliki status aktif hamil maka kembalikan error
                if($person->pregnant->firstWhere('status', 'aktif')) {
                    return $this->httpResponseError(false, 'pregnant already have status active', [], 409);
                }

                $pregnant = Pregnant::create([
                    'peopleId' => $person->id,
                    'pregnancyStartDate' => $request->pregnancyStartDate,
                    'estimatedDueDate' => $request->estimatedDueDate,
                    'husbandName' => $request->husbandName,
                    'actualDeliveryDate' => $request->actualDeliveryDate,
                    'status' => $request->status
                ]);

                Cache::tags(['pregnants'])->flush();

                return $this->httpResponse(true, 'Created success', $pregnant, 201);
            } else {
                return $this->httpResponseError(false, 'You dont have access', '', 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('pregnants-update')) {

                $pregnant = Pregnant::find($id);

                if(!$pregnant) {
                    return $this->httpResponseError(false, 'not found', [], 404);
                }
                
                if( 
                    $request->has('status') &&
                    $request->has('actualDeliveryDate') &&
                    count($request->all()) === 2
                ) {
                    $validator = Validator::make($request->only(['status', 'actualDeliveryDate']), [
                        'status' => 'required|in:melahirkan,selesai',
                        'actualDeliveryDate' => 'required|date',
                    ]);

                    if($validator->fails()){
                        return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                    }

                    $pregnant->status = $request->status;

                    $pregnant->actualDeliveryDate = $request->actualDeliveryDate;
                    
                    $pregnant->save();

                    Cache::tags(['pregnants'])->flush();

                    return $this->httpResponse(true, 'success', $pregnant, 200);
                }

                $validator = Validator::make($request->all(), [
                    'pregnancyStartDate' => 'required|date',
                    'estimatedDueDate' => 'required|date',
                    'husbandName' => 'required|string',
                    'status' => 'required|in:aktif,melahirkan,selesai'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                }

                $pregnant->update($request->only([
                    'pregnancyStartDate',
                    'estimatedDueDate',
                    'husbandName',
                    'status'
                ]));
                
                Cache::tags(['pregnants'])->flush();

                return $this->httpResponse(true, 'success', $pregnant, 200);
            } else {
                return $this->httpResponseError(false, 'You dont have access', '', 403);
            }
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
