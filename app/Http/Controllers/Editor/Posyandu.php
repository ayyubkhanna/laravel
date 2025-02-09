<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Posyandu extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posyandu = \App\Models\Posyandu::all();

            if($posyandu) {
                return $this->httpResponse(true, 'Success', $posyandu, 200);
            } else {
                return $this->httpResponse(false, 'Data not found', $posyandu, 404);
            }
        } catch (\Throwable $th) {
            return $this->httpResponse(false, 'Error', $th->getMessage(), 500);
        }


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            if(request()->user()->hasRole(['admin']) || request()->user()->isAbleTo('posyandu-create')) {

                $validator = Validator::make($request->all(), [
                    'name' => ['required'],
                    'alamat' => ['required'],
                    'deskripsi' => ['required'],
                ]);

                if($validator->fails()){
                    return $this->httpResponse(false, 'Validation Failed', $validator->getMessageBag(), 244);
                }

                $posyandu = \App\Models\Posyandu::create([
                    'name' => $request->name,
                    'alamat' => $request->alamat,
                    'deskripsi' => $request->deskripsi
                ]);

                return $this->httpResponse(true, 'Created posyandu success', $posyandu, 201);
            }

        } catch (\Throwable $th) {
            return $this->httpResponse(false, 'Error', $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $posyandu = \App\Models\Posyandu::findOrFail($id);

            return $this->httpResponse(true, 'success', $posyandu, 200);
        } catch (\Throwable $th) {
            return $this->httpResponse(false, $th->getMessage(), '', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if(request()->user()->hasRole(['admin']) || request()->user()->isAbleTo('posyandu-delete')) {
                $posyandu = \App\Models\Posyandu::findOrFail($id);

                $posyandu->delete();

                return $this->httpResponse(true, 'Deleted success', "", 200);
            }
        } catch (\Throwable $th) {
            return $this->httpResponse(false, $th->getMessage(), "", 500);
        }
    }
}
