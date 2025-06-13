<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Pregnant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChartController extends Controller
{
    public function childChart()
    {

        $currentYear = Carbon::now()->year;

        $child = Child::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                        ->whereYear('created_at', $currentYear)
                        ->groupBy('month')
                        ->orderBy('month', 'asc')
                        ->get();

        $formatData = [];

        for ($i=0; $i <= Carbon::now()->month ; $i++) {
            $formatData[] = [
                'name' => Carbon::create()->month($i)->format('M'),
                'value' => $child->where('month', $i)->first()->total ?? 0
            ];
        }

        return $this->httpResponse(true, 'Success', $formatData, 200);
    }

    public function total()
    {
        $child = Child::count();

        $pregnant = Pregnant::count();

        return response()->json([
            'pregnant' => $pregnant,
            'child' => $child,
        ], 200);

    }
}
