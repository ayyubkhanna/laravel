<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

            $cacheVersion = Cache::get('children_version', 1);

            $cacheKey = "children_{$cacheVersion}_page_{$page}_entries_{$entries}_pencarian_{$search}_filter_{$filter}";

            $children = Cache::remember($cacheKey, now()->addMinutes(5), function ()use ($entries, $search, $filter) {

                $child =  Child::with('posyandu')
                ->when($filter, function ($query) use ($filter) {
                        return $query->where('posyandu_id', $filter);

                })
                ->when($search, function ($query) use ($search, $filter) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                        ->orWhere('kia', 'like', "%$search%")
                        ->orWhere('alamat', 'like', "%$search%");

                    });
                })
                ->paginate($entries);

                return $child;
            });

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
