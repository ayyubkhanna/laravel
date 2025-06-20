<?php

namespace App\Http\Controllers\Editor\Child;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Stunting;
use App\Models\Weighing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WeighingController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getGrowthData($age)
    {
        // Data rata-rata tinggi badan dan deviasi standar berdasarkan usia anak menurut standar WHO
        $growthData = [
            0  => ['averageHeight' => 50.0, 'standardDeviation' => 2.0],
            3  => ['averageHeight' => 60.5, 'standardDeviation' => 2.5],
            6  => ['averageHeight' => 67.5, 'standardDeviation' => 3.0],
            12 => ['averageHeight' => 75.5, 'standardDeviation' => 3.5],
            18 => ['averageHeight' => 80.0, 'standardDeviation' => 4.0],
            24 => ['averageHeight' => 85.5, 'standardDeviation' => 6.0],
            36 => ['averageHeight' => 95.0, 'standardDeviation' => 7.0],
            48 => ['averageHeight' => 103.5, 'standardDeviation' => 8.0],
            60 => ['averageHeight' => 110.5, 'standardDeviation' => 9.0],
        ];

        // Menyesuaikan usia anak untuk data pertumbuhan terdekat
        $closestAge = floor($age / 12) * 12; // Pembulatan ke tahun terdekat
        $closestAge = $closestAge > 60 ? 60 : $closestAge; // Maksimum usia 60 bulan

        return $growthData[$closestAge] ?? ['averageHeight' => 0, 'standardDeviation' => 0];
    }

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
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('weighing-create')) {
                $validator = Validator::make($request->all(), [
                    'childId' => 'required|exists:children,id',
                    'date' => 'required|date',
                    'age' => 'required|integer',
                    'bodyWeight' => 'required|integer',
                    'bodyHeight' => 'required|integer',
                    'information' => 'nullable|string'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation Error', $validator->errors(), 422);
                }

                $growthData = $this->getGrowthData($request->age);
                $averageHeight = $growthData['averageHeight'];
                $standardDeviation = $growthData['standardDeviation'];

                $zScore = ($request->length_body - $averageHeight) / $standardDeviation;

                // Menentukan apakah anak terindikasi stunting
                $isStunted = $zScore < -2;

                $children = Child::findOrFail($request->childId);

                $data = collect();

                // menangani jika stunting bermasalah maka checkup jangan sampe tersimpan didatabase
                DB::transaction(function () use ($children, $request, $isStunted, $data) {

                    // simpan data checkup
                    $collect = $children->weighings()->create([
                        'date' =>$request->date,
                        'age' => $request->age,
                        'bodyWeight' => $request->bodyWeight,
                        'bodyHeight' => $request->bodyHeight,
                        'information' => $request->information
                    ]);

                    // simpan data stunting
                    Stunting::create([
                        'status' => $isStunted ? 'aktif':'tidak_aktif',
                        'weightId' => $collect->id,
                    ]);

                    $data->push($collect);

                });

                Cache::tags(['children'])->flush();

                return $this->httpResponse(true, 'Created Successfully', $data, 201);
            } else {
                return $this->httpResponseError(false, 'You dont have access', [], 403);
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
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('weighing-update')) {

                $data = Weighing::where('id', $id)->with('stunting')->firstOrFail();

                $validator = Validator::make($request->all(), [
                    'date' => 'required|date',
                    'age' => 'required|integer',
                    'bodyWeight' => 'required|integer',
                    'bodyHeight' => 'required|integer',
                    'information' => 'nullable|string'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation Error', $validator->errors(), 422);
                }

                $growthData = $this->getGrowthData($request->age);
                $averageHeight = $growthData['averageHeight'];
                $standardDeviation = $growthData['standardDeviation'];

                $zScore = ($request->length_body - $averageHeight) / $standardDeviation;

                // Menentukan apakah anak terindikasi stunting
                $isStunted = $zScore < -2;

                $data->update($request->only(['date', 'age','bodyWeight', 'bodyHeight', 'information']));

                $data->stunting()->update([
                    'status' => $isStunted ? 'aktif' : 'tidak_aktif'
                ]);

                $data->load('stunting');

                Cache::tags(['children'])->flush();

                return $this->httpResponse(true, 'Updated Successfully', $data, 200);
            } else {
                return $this->httpResponseError(false, 'You dont have access', [], 403);
            }
        } catch (ModelNotFoundException $e) {
            return $this->httpResponseError(false, 'Data not found', $e->getMessage(), 404);
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
            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('weighing-delete')) {

                $weighing = Weighing:: find($id);

                $weighing->delete();

                Cache::tags(['children'])->flush();

                return $this->httpResponse(true, 'Deleted success', [], 200);
            } else {
                return $this->httpResponseError(false, 'Data not found', [], 404);
            }
        } catch (ModelNotFoundException $e) {
            return $this->httpResponseError(false, 'Data not found', $e->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(), 500);
        }
    }
}
