<?php

namespace App\Http\Controllers\Editor\Pregnant;

use App\Http\Controllers\Controller;
use App\Models\PregnantCheckup;
use Illuminate\Http\Request;

class CheckupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $checkups = PregnantCheckup::all();


        } catch (\Throwable $th) {
            //throw $th;
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
