<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spreadsheet;


class ResourceController extends Controller
{
    public function getRam(){
        $response = Spreadsheet::where('status',1)
            ->select(['ram'])
            ->distinct()
            ->get();

        return response()->json([
                'hasError' => false,
                'data' => $response
            ], 200);
    }

    public function getHddType(){
        $response = Spreadsheet::where('status',1)
            ->select(['hard_disk_type'])
            ->distinct()
            ->get();

        return response()->json([
                'hasError' => false,
                'data' => $response
            ], 200);
    }

    public function getLocation(){
        $response = Spreadsheet::where('status',1)
            ->select(['location'])
            ->distinct()
            ->get();

        return response()->json([
                'hasError' => false,
                'data' => $response
            ], 200);
    }
}
