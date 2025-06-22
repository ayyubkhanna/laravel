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

            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('pregnants-create')) {
                $validator = Validator::make($request->all(), [
                    'people_id' => 'required|integer|unique:pregnants,people_id|exists:people,id',
                    'awal_kehamilan' => 'required|date',
                    'perkiraan_hamil' => 'required|date',
                    'nama_suami' => 'required',
                    'status' => 'required|in:aktif,melahirkan,selesai'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                }

                $person = Person::findOrFail($request->people_id);
                
                if($person->child) {
                    return $this->httpResponseError(false, 'person already status child', [], 400);
                }

                $pregnant = Pregnant::create([
                    'people_id' => $person->id,
                    'awal_kehamilan' => $request->awal_kehamilan,
                    'perkiraan_hamil' => $request->perkiraan_hamil,
                    'nama_suami' => $request->nama_suami,
                    'status' => $request->status
                ]);

                Cache::tags(['pregnants'])->flush();

                return $this->httpResponse(true, 'Created success', $pregnant, 201);
            } else {
                return $this->httpResponseError(false, 'You dont have access', '', 403);
            }
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'not found', $th->getMessage(), 404);
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
            if(request()->user()->hasRole('editor') && request()->user()->isAbleTo('pregnants-update')) {

                $pregnant = Pregnant::find($id);

                if(!$pregnant) {
                    return $this->httpResponseError(false, 'not found', [], 404);
                }

                $validator = Validator::make($request->all(), [
                    'awal_kehamilan' => 'required|date',
                    'perkiraan_hamil' => 'required|date',
                    'nama_suami' => 'required',
                    'status' => 'required|in:aktif,melahirkan,selesai'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                }

                $pregnant->update($request->only(['awal_kehamilan', 'perkiraan_hamil', 'nama_suami', 'status']));

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
        try {
            
            // cek role dari user
            if(request()->user()->hasRole(['editor', 'admin']) || request()->user()->isAbleTo('delete-pregnant')) {

                $pregnant = Pregnant::find($id);

                // cek jika checkup tidak ada
                if(!$pregnant) {
                    return $this->httpResponseError(false, 'checkup not found', [], 404);
                }

                $pregnant->delete();

                return $this->httpResponse(true, 'deleted success', [], 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(),500);
        }
    }
}
