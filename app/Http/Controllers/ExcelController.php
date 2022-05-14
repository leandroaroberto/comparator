<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use App\Models\Spreadsheet;

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
    private function createSpreadsheetOnDatabase($excelFile = 'LeaseWeb_servers_filters_assignment.xlsx'){
        $excelFile = storage_path('app/public/' . $excelFile);
        
        $reader = new Xlsx();
        $spreadsheet = $reader->load($excelFile);
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $data = $sheet->toArray();    
        
        try{
            foreach($data as $rowIndex => $row){
                if ($rowIndex == 0){
                 continue;
                }                
                // Spreadsheet::create([
                //     'model_name'        => $row[0],
                //     'ram'               => $this->getRam($row[1]),
                //     'ram_type'          => $this->getRamType($row[1]),
                //     'hard_disk_storage' => $this->getHardDiskStorage($row[2]),
                //     'hard_disk_amount'  => $this->getHardDiskAmount($row[2]),
                //     'hard_disk_type'    => $this->getHardDiskType($row[2]),
                //     'location'          => $row[3],
                //     'price'             => $this->getPrice($row[4]) 
                // ]);

                Spreadsheet::create([
                    'model_name'        => $row[0],
                    'ram'               => $row[1],
                    'ram_type'          => $row[1],
                    'hard_disk_storage' => $row[2],
                    'hard_disk_amount'  => 2,
                    'hard_disk_type'    => $row[2],
                    'location'          => $row[3],
                    'price'             => floatval($row[4])
                ]);
            }
        } catch(Throwable $exception){
            return [
                'hasError' => true,
                'message' => 'Error on insert data in Spreadsheet table.',
                'errorDetails'=> $exception
            ];
        }

        return [
                'hasError' => false,
                'message' => 'Catalog updated successfully.',
            ];

    }

    private function getRam($ram){        
        $ram = explode('GB', strtoupper($ram))[0];
        return $ram . 'DDR';
    }

    private function getRamType($ram){
        return explode(strtoupper($ram) , 'GB')[1];        
    }

    private function getHardDiskStorage($hdd){
        $hdd = strtoupper($hdd);
        $hdd = explode(strtoupper($hdd),'X')[1];
        return strpos($hdd, 'GB') ? explode($hdd,'GB')[0] . 'GB' : strpos($hdd, 'TB') ? explode($hdd,'TB')[0] . 'TB' : explode($hdd,'MB')[0] . 'MB';
    }

    private function getHardDiskAmount($hdd){
        return explode(strtoupper($hdd),'X')[0];
    }

    private function getHardDiskType($hdd){
        $hdd = $this->getHardDiskStorage($hdd);
        $diskType = substr($hdd, -2, -1);
        return explode($hdd, $diskType)[1];
    }

    private function getPrice($price){
        return floatval($price);
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
    
    // /**
    //  * Display the specified resource.
    //  *
    //  * @return array
    //  */
    // public static function getData(){        
    //     $data = session('SEARCH_DATA');
    //     return $data ?: [];
    // }

}
