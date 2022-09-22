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
        //$data = ['not implemented yet.', $request->all()];
       
        $data = DB::table('spreadsheets')
            ->where('status',1)
            //->whereIn('ram', ['2GB', '4GB', '128GB'])
            ->where('location',$request->location ?: '')
            ->where('hard_disk_type',$request->harddisk ?: '')
            ->orderBy('ram','DESC')
            ->orderBy('model_name','ASC')
            ->get();

        return response()->json($data, 200);
    }
}
