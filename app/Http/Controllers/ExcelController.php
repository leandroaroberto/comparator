<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
//use \PhpOffice\PhpSpreadsheet\Writer\Csv;

class ExcelController extends Controller
{
    public function convertSpreadsheetToJson($excelFile = 'LeaseWeb_servers_filters_assignment.xlsx'){
        $excelFile = storage_path('app/public/' . $excelFile);
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($excelFile);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();
        return $data;

        //$loadedSheetNames = $spreadsheet->getSheetNames();
        // $writer = new Csv($spreadsheet);
        // foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
        //     $writer->setSheetIndex($sheetIndex);
        //     $writer->save($loadedSheetName.'.csv');
        // }
    }

}
