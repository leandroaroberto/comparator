<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\DB;
use App\Models\Spreadsheet;

class HomeController extends Controller
{
    
    public function listAll(){
        $data = Spreadsheet::where('status',1)
            ->orderBy('model_name', 'ASC')
            ->get();
        return response()->json($data, 200);
    }

    public function showById($id){
        $data = Spreadsheet::where('id',$id)
            ->where('status',1)
            ->get();
        return response()->json($data, 200);
    }

    public function searchByParams(Request $request){
        /*
        "storage": null,
		"ram": null,
		"harddisk": null,
		"location": null
        */

        $data = DB::table('spreadsheets')->select('*');
        // if ($request->storage)
        //     $data = $data->where('storage', $request->storage);
        if($request->ram)
            $data = $data->whereIn('ram', $request->ram);
        if ($request->location)
            $data = $data->where('location', $request->location);
        if ($request->harddisk)
            $data = $data->where('hard_disk_type', $request->harddisk);
        $data = $data->get();
        
        return response()->json($data, 200);
    }
}
