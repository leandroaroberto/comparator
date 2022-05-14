<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SpreadsheetImport;

class DashboardController extends Controller
{
    public function index(){
        return view('dashboard');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function fileImport(Request $request) 
    {
        Excel::import(new SpreadsheetImport, $request->file('file')->store('temp'));
        return back();
    }
}
