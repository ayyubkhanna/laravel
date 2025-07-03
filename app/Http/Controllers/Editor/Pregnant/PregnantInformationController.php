<?php

namespace App\Http\Controllers\Editor\Pregnant;

use App\Http\Controllers\Controller;
use App\Models\Pregnant;
use App\Models\PregnantInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PregnantInformationController extends Controller
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
            if($request->user()->hasRole(['admin', 'editor']) || $request->user()->isAbleTo('create-pregnant-information')) {
                $validator = Validator::make($request->all(), [
                    'pregnantId' => 'required|integer|exists:pregnants,id',
                    'husbandName' => 'nullable|string',
                    'numberPhone' => 'nullable|numeric',
                    'religion' => 'required|string',
                    'job' => 'required|string',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
                ]);

                if($validator->fails()) {
                    return $this->httpResponseError(false, 'validation failed', $validator->errors(), 422);
                }

                $pregnant = Pregnant::where('id', $request->pregnantId)->first();

                $newFileName = null;

                if($request->hasFile('image')) {
                    $file = $request->file('image');

                    // ambil extension dari foto
                    $ext = $file->getClientOriginalExtension();

                    // buat nama baru
                    $newFileName = Str::uuid() . ".$ext";

                    // simpan nama di storage
                    $file->storeAs('pregnant/image', $newFileName);

                }

                $data = $pregnant->pregnantInformation()->create([
                    'husbandName' => $request->hasbandName,
                    'numberPhone' => $request->numberPhone,
                    'religion' => $request->religion,
                    'job' => $request->job,
                    'image' => $newFileName
                ]);

                return $this->httpResponse(true, 'success', $data, 201);
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
            if($request->user()->hasRole('editor') || $request->user()->isAbleTo('update-pregnant-information')) {

                $detail = PregnantInformation::where('id', $id)->first();


                // menghandel jika hanya mengganti foto profil
                if($request->hasFile('image')) {
                    $validator = Validator::make($request->all(), [
                        'image' => 'required|image|mimes:jpeg,jpg,png|max:2048'
                    ]);

                    if($detail->image && Storage::exists("pregnant/image/$detail->image")) {
                        Storage::delete("pregnant/image/$detail->image");
                    }

                    $file = $request->file('image');

                    // ambil extension dari foto
                    $ext = $file->getClientOriginalExtension();

                    // buat nama baru
                    $newFileName = Str::uuid() . ".$ext";

                    // simpan nama di storage
                    $file->storeAs('pregnant/image', $newFileName);

                    $detail->image = $newFileName;

                    $detail->save();

                    return $this->httpResponse(true, 'updated image success', [], 200);
                }

                // menghandel jika update informasi
                $validator = Validator::make($request->all(), [
                    'husbandName' => 'nullable|string',
                    'numberPhone' => 'nullable|numeric',
                    'religion' => 'required|string',
                    'job' => 'required|string',
                ]);

                if($validator->fails()) {
                    return $this->httpResponseError(false, 'validation failed', $validator->errors(), 422);
                }

                $detail->husbandName = $request->husbandName;
                $detail->numberPhone = $request->numberPhone;
                $detail->religion = $request->religion;
                $detail->job = $request->job;

                $detail->save();

                return $this->httpResponse(true, 'updated success', $detail, 200);
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
        //
    }
}
