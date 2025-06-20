<?php

namespace App\Http\Controllers\Editor\Child;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Immunization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ImmunizationController extends Controller
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
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('immunization-create')) {

                // Validasi data yang masuk
                $validator = Validator::make($request->all(), [
                    'childId' => 'required|exists:children,id',
                    'date' => 'required|date',
                    'type' => 'required|string'
                ]);

                // handel validasi error
                if($validator->fails()) {
                    return $this->httpResponseError(false, 'validation failed', $validator->errors(), 422);
                }

                // ambil data children
                $children = Child::find($request->childId);

                // jika children tidak ditemukan
                if(!$children) {
                    return $this->httpResponseError(false, 'child not found', [], 404);
                }

                // masukan data imunisasi ke database 
                $data = $children->immunizations()->create([
                    'date' => $request->date,
                    'type' => $request->type
                ]);

                Cache::tags(['children'])->flush();

                // response api 
                return $this->httpResponse(true, 'created success', $data, 201);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
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
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('immunization-update')) {

                // Validasi data yang masuk
                $validator = Validator::make($request->all(), [
                    'date' => 'required|date',
                    'type' => 'required|string'
                ]);

                // handel validasi error
                if($validator->fails()) {
                    return $this->httpResponseError(false, 'validation failed', $validator->errors(), 422);
                }

                // ambil data children
                $children = Child::find($id);

                // jika children tidak ditemukan
                if(!$children) {
                    return $this->httpResponseError(false, 'child not found', [], 404);
                }

                // masukan data imunisasi ke database 
                $data = $children->immunizations()->update([
                    'date' => $request->date,
                    'type' => $request->type
                ]);

                Cache::tags(['children'])->flush();

                // response api 
                return $this->httpResponse(true, 'updated success', $data, 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('immunization-delete')) {

                $immun = Immunization:: find($id);

                // cek apakah imun ada
                if(!$immun) {
                    return $this->httpResponseError(false, 'Data not found', [], 404);
                }

                // hapus immun
                $immun->delete();

                Cache::tags(['children'])->flush();

                return $this->httpResponse(true, 'Deleted success', [], 200);
            } else {
                return $this->httpResponseError(false, 'Data not found', [], 404);
            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(), 500);
        }
    }
}
