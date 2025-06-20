<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Stunting;
use Illuminate\Http\Request;

class StuntingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->user()->hasRole(['admin', 'editor']) || $request->user()->isAbleTo('read-stunting')) {
                $stunting = Stunting::with(['weighings' => fn($query) => $query->orderByDesc('date'), 'weighings.child.pregnant.person'])
                ->where('status', 'aktif')
                ->get();
            
    
                if(!$stunting) {
                    return $this->httpResponseError(false, 'data not found', [], 404);
                }
                
                return $this->httpResponse(true, 'success', $stunting, 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);

            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(), 500);
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
        try {
            if(request()->user()->hasRole(['admin', 'editor']) || request()->user()->isAbleTo('read-stunting')) {
                $stunting = Stunting::with('childcheckup.child')
                            ->where('id', $id)
                            ->get();
    
                if(!$stunting) {
                    return $this->httpResponseError(false, 'data not found', [], 404);
                }
                
                return $this->httpResponse(true, 'success', $stunting, 200);
            } else {
                return $this->httpResponseError(false, 'you dont have access', [], 403);

            }
        } catch (\Throwable $th) {
            return $this->httpResponseError(false, 'ERROR', $th->getMessage(), 500);
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
        //
    }
}
