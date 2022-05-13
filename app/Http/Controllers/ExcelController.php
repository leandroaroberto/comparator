<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
//use \PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ExcelController extends Controller
{
    const VALID_MIME_TYPES = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    const VALID_EXTENSIONS = [
        'xls',
        'xlsx'
    ];

    /**
     * @param $excelFile
     * @return bool
     */
    private function convertSpreadsheetToJson($excelFile = 'LeaseWeb_servers_filters_assignment.xlsx'){
        $excelFile = storage_path('app/public/' . $excelFile);
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($excelFile);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();   

        if(count($data)){
            session(['SEARCH_DATA' => $data]);  
            return true;
        }
        return false;        

        //$loadedSheetNames = $spreadsheet->getSheetNames();
        // $writer = new Csv($spreadsheet);
        // foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
        //     $writer->setSheetIndex($sheetIndex);
        //     $writer->save($loadedSheetName.'.csv');
        // }
    }
    
    public function upload(Request $request){        
        $file = $request->file;
        $fileMimeType = $file->getMimeType();
        $originalFileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileSize = ($file->getSize() / 1000);
        $fileHashName = md5(now() . $originalFileName) . '.' . $extension;     
        
        if (!$this->isValidFileType($file)) {            
            return [
                'hasError' => true,
                'message' => 'File type not supported.'
            ];
        }

        try{
            $path = $file->storeAs('public', $fileHashName);
        }
        catch (Throwable $e) {
            return [
                'hasError' => true,
                'message' => 'Failed to upload, please try again.'
            ];
        }
        
        if ($this->convertSpreadsheetToJson($fileHashName)){
            $data = session('SEARCH_DATA');
            return [
                'hasError' => false,
                'message' => '',
                'data' => $data
            ];
        }
    }

     /**
     * @param $file
     * @return bool
     */
    private function isValidFileType($file): bool
    {
        if (!in_array($file->getClientOriginalExtension(), self::VALID_EXTENSIONS)) {
            Log::info("Invalid extension for {$file->getClientOriginalName()}: {$file->getClientOriginalExtension()}");
            return false;
        }

        // the client supplied mime type wasn't valid.
        if (!in_array($file->getClientMimeType(), self::VALID_MIME_TYPES) && !in_array($file->getMimeType(), self::VALID_MIME_TYPES)) {
            Log::info("Invalid client and guessed mime type for {$file->getClientOriginalName()}: {$file->getClientMimeType()} / {$file->getMimeType()}");
            return false;
        }

        return true;
    }    

}
