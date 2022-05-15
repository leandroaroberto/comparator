<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\DB;
use App\Models\Spreadsheet;

class SpreadsheetController extends Controller
{
    const VALID_MIME_TYPES = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    const VALID_EXTENSIONS = [
        'xls',
        'xlsx'
    ];


    public function __construct(){
        $this->middleware('auth:api');
    }

    /**
     * @param $excelFile
     * @return bool
     */
    private function createSpreadsheetOnDatabase($excelFile = 'LeaseWeb_servers_filters_assignment.xlsx'){
        $excelFile = storage_path('app/public/' . $excelFile);
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($excelFile);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();  
        
        try{
            DB::transaction(function() use ($data){
                DB::table('spreadsheets')->delete();
                foreach($data as $rowIndex => $row){
                    if ($rowIndex == 0){
                        continue;
                    }
                    Spreadsheet::create([
                        'model_name'        => $row[0],
                        'ram'               => $this->getRam($row[1]),
                        'ram_type'          => $this->getRamType($row[1]),
                        'hard_disk_storage' => $this->getHardDiskStorage($row[2]),
                        'hard_disk_amount'  => $this->getHardDiskAmount($row[2]),
                        'hard_disk_type'    => $this->getHardDiskType($row[2]),
                        'location'          => $row[3],
                        'price'             => $row[4] 
                    ]);
                }
            });
        } catch(Throwable $exception){
            return response()->json([
                'hasError' => true,
                'message' => 'Error on insert data in Spreadsheet table. Spreadsheet bad format. Please contact Admin for more details.',
                'errorDetails'=> $exception
            ], 400);
        }

        return response()->json([
                'hasError' => false,
                'message' => 'Catalog has updated successfully.',
            ], 201);

    }

    public function getRam($ram){        
        $ram = explode('GB', strtoupper($ram))[0];
        return $ram . 'GB';
    }

    public function getRamType($ram){
        return explode('GB', strtoupper($ram))[1];        
    }

    public function getHardDiskStorage($hdd){
        $hdd = strtoupper($hdd);
        $hdd = explode('X',$hdd)[1];
        if (strpos($hdd, 'GB')){
            return explode('GB',$hdd)[0] . 'GB';
        }
        elseif (strpos($hdd, 'TB')){
            return explode('TB',$hdd)[0] . 'TB';
        }
        else{
            return explode('MB', $hdd)[0] . 'MB';
        }
    }

    public function getHardDiskAmount($hdd){
        return explode('X', strtoupper($hdd))[0];
    }

    public function getHardDiskType($hdd){
        $hdd = strtoupper($hdd);
        $hdd = explode('X',$hdd)[1];
        if (strpos($hdd, 'GB')){
            return explode('GB',$hdd)[1];
        }
        elseif (strpos($hdd, 'TB')){
            return explode('TB',$hdd)[1];
        }
        else{
            return explode('MB', $hdd)[1];
        }
    }  

    public function upload(Request $request){        
        $file = $request->file;
        $fileMimeType = $file->getMimeType();
        $originalFileName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileSize = ($file->getSize() / 1000);
        $fileHashName = md5(now() . $originalFileName) . '.' . $extension;     
        
        if (!$this->isValidFileType($file)) {            
            return response()->json([
                'hasError' => true,
                'message' => 'File type not supported.'
            ], 400);
        }

        try{
            $path = $file->storeAs('public', $fileHashName);
        }
        catch (Throwable $e) {
            return response()->json([
                'hasError' => true,
                'message' => 'Failed to upload, please try again.'
            ], 400);
        }

        return $this->createSpreadsheetOnDatabase($fileHashName);
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