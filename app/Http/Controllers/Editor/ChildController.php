<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ChildController extends Controller
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

            $children =  Child::with('posyandu')
                ->when($filter, function ($query) use ($filter) {
                        return $query->where('posyandu_id', $filter);

                })
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                        ->orWhere('kia', 'like', "%$search%")
                        ->orWhere('alamat', 'like', "%$search%");

                    });
                })
                ->paginate($entries);

            Cache::tags(['children'])->put('children', $children, now()->addMinutes(5));


            if(request()->user()->hasRole(['admin', 'editor']) || request()->user()->isAbleTo('children-read', $children)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $children
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

            if(request()->user()->hasRole('editor') || request()->user()->isAbleTo('children-create')){

                $validator = Validator::make($request->all(), [
                    'kia' => 'required|integer',
                    'name' => 'required',
                    'nik' => 'required|integer',
                    'alamat' => 'required',
                    'orang_tua' => 'required',
                    'posyandu_id' => 'required|integer',
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'validation failed', $validator->getMessageBag(), 422);
                }

                $children = Child::create($request->all());


                return $this->httpResponse(true, 'created success', $children, 201);

            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
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
            $child = Child::findOrFail($id);
            $child->load('checkup');

            return $this->httpResponse(true, 'success', $child, 200);
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'Data not found', $th->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
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
                    'kia' => 'required|integer',
                    'name' => 'required',
                    'nik' => 'required|integer',
                    'alamat' => 'required',
                    'orang_tua' => 'required',
                    'posyandu_id' => 'required',
                ]);

                if($validator->fails()){
                    return $this->httpResponseError(false, 'validation failed', $validator->getMessageBag(), 422);
                }

                $children->update($request->all());

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
        try {
            if(request()->user()->hasRole('editor') && request()->user()->isAbleTo('children-delete')){
                $child = Child::findOrFail($id);

                $child->delete();

                return $this->httpResponse(true, 'deleted success', [], 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);
            }
        } catch (ModelNotFoundException $th) {
            return $this->httpResponseError(false, 'Data not found', $th->getMessage(), 404);
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'Error', $th->getMessage(), 500);
        }
    }
}
