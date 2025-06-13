<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Posyandu;
use App\Models\Pregnant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PregnantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {

            $page = $request->query('page', 1);

            $entries = $request->query('entries', 10);

            $search = $request->query('pencarian');

            $filter = $request->query('filter');

            $pregnants =  Pregnant::with('posyandu')
                ->when($filter, function ($query) use ($filter) {
                        return $query->where('posyandu_id', $filter);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                        ->orWhere('nama_suami', 'like', "%$search%")
                        ->orWhere('nik', 'like', "%$search%");

                    });
                })->paginate($entries);

            Cache::tags(['pregnants'])->put('pregnants', $pregnants, now()->addMinutes(5));

            if(request()->user()->hasRole(['admin', 'editor']) || request()->user()->isAbleTo('pregnant-read', $pregnants)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $pregnants
                ], 200);
            } else {
                return $this->httpResponse(false, 'Anda tidak punya akses untuk melakukan ini', '', 403);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found',
                'data' => $th->getMessage()
            ], 404);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            if(request()->user()->hasRole('editor') && request()->user()->isAbleTo('pregnants-create')) {
                $validator = Validator::make($request->all(), [
                    'name' => ['required'],
                    'nik' => 'required|integer',
                    'alamat' => 'required',
                    'awal_kehamilan' => 'required|date',
                    'perkiraan_hamil' => 'required|date',
                    'nama_suami' => 'required',
                    'posyandu_id' => 'required'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                }

                $pregnant = Pregnant::create($request->all());

                $pregnant->load('posyandu');

                Cache::tags(['pregnants'])->flush();

                return $this->httpResponse(true, 'Created success', $pregnant, 200);
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
    public function show(string $id)
    {
        try {
            $pregnant = Pregnant::find($id);

            if(!$pregnant) {
                return $this->httpResponseError(false, 'data not found', [], 404);
            }

            $pregnant->load('checkups');

            return $this->httpResponse(true, 'Success', $pregnant, 200);
        }  catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
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
                    'name' => ['required'],
                    'nik' => 'required|integer',
                    'alamat' => 'required',
                    'awal_kehamilan' => 'required|date',
                    'perkiraan_hamil' => 'required|date',
                    'nama_suami' => 'required',
                    'posyandu_id' => 'required'
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'Validation failed', $validator->getMessageBag(), 422);
                }

                $pregnant->update($request->only(['name', 'nik', 'alamat', 'awal_kehamilan', 'perkiraan_hamil', 'nama_suami', 'posyandu_id']));

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
            $pregnant = Pregnant::findOrFail($id);

            $pregnant->delete();

            return $this->httpResponse(true, 'Deleted pregnant data success', [], 200);
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'not found', $th->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
    }
}
