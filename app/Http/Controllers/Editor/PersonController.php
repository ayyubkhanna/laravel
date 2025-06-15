<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Person;
use App\Models\Posyandu;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            if($request->user()->hasRole(['editor', 'admin']) || $request->user()->isAbleTo('index-person')) {

                $entries = $request->query('entries', 10);

                $search = $request->query('pencarian');

                $filter = $request->query('filter');

                $children =  Person::with(['child', 'pregnant'])
                    ->when($filter, function ($query) use ($filter) {
                            return $query->where('posyandu_id', $filter);
                    })
                    ->when($search, function ($query) use ($search) {
                        return $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%")
                            ->orWhere('nik', 'like', "%$search%");

                        })
                        ->orWhereHas('child', function ($q) use ($search) {
                            $q->where('kia', 'like', "%$search%");
                        })->orWhereHas('pregnant', function ($q) use ($search) {
                            $q->where('nik', 'like', "%$search%");
                        });
                    })
                    ->paginate($entries);

                Cache::tags(['children'])->put('children', $children, now()->addMinutes(5));

                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $children
                ], 200);
            } else {
                return $this->httpResponse(false, 'you dont have access', [], 403);
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
            if($request->user()->hasRole('editor') || $request->user()->isAbleTo('create-person')) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    'tempat_lahir' => 'required|string',
                    'tanggal_lahir' => 'required|date',
                    'alamat' => 'required',
                    'jenis_kelamin' => 'required|in:laki-laki,perempuan',
                    'posyandu_id' => 'required|integer'
                ]);

                if($validator->fails()) {
                    return $this->httpResponse(false, 'validation failed', $validator->errors(), 422);
                }

                $posyandu = Posyandu::where('id', $request->posyandu_id)->firstOrFail();

                $persons = Person::create([
                    'name' => $request->name,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'posyandu_id' => $posyandu->id
                ]);

                return $this->httpResponse(true, 'created data', $persons, 201);
            } else {
                return $this->httpResponse(false, 'you dont have access', [], 403);
            }
        } catch (ModelNotFoundException $e) {
            return $this->httpResponseError(false, 'Data not found', $e->getMessage(), 404);
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
            if(request()->user()->hasRole(['editor', 'admin']) || request()->user()->isAbleTo('show-person')) {
                $person = Person::findOrFail($id);
                $person->load([
                    'child.checkups', 
                    'pregnant.checkups'
                ]);

                return $this->httpResponse(true, 'success', $person, 200);
            } else {
                return $this->httpResponse(false, 'you dont have access', [], 403);
            }
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
            if($request->user()->hasRole('editor') || $request->user()->isAbleTo('update-person')) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    'tempat_lahir' => 'required|string',
                    'tanggal_lahir' => 'required|date',
                    'alamat' => 'required',
                    'jenis_kelamin' => 'required|in:laki-laki,perempuan',
                    'posyandu_id' => 'required|integer'
                ]);

                if($validator->fails()) {
                    return $this->httpResponse(false, 'validation failed', $validator->errors(), 422);
                }

                $posyandu = Posyandu::where('id', $request->posyandu_id)->firstOrFail();

                $person = Person::findOrFail($id);

                $person->update([
                    'name' => $request->name,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'alamat' => $request->alamat,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'posyandu_id' => $posyandu->id
                ]);

                return $this->httpResponse(true, 'created data', $person, 201);
            } else {
                return $this->httpResponse(false, 'you dont have access', [], 403);
            }
        } catch (ModelNotFoundException $e) {
            return $this->httpResponseError(false, 'Data not found', $e->getMessage(), 404);
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
            if(request()->user()->hasRole('editor') && request()->user()->isAbleTo('person-delete')){
                $person = Person::findOrFail($id);

                $person->delete();

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
