<?php
namespace App\Imports;
use App\Models\Spreadsheet;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SpreadsheetImport implements ToModel, WithBatchInserts
{

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // return new Spreadsheet([
        //     'model_name'        => $row[0],
        //     'ram'               => $this->getRam($row[1]),
        //     'ram_type'          => $this->getRamType($row[1]),
        //     'hard_disk_storage' => $this->getHardDiskStorage($row[2]),
        //     'hard_disk_amount'  => $this->getHardDiskAmount($row[2]),
        //     'hard_disk_type'    => $this->getHardDiskType($row[2]),
        //     'location'          => $row[3],
        //     'price'             => $this->getPrice($row[4])           
        // ]);

        return new Spreadsheet([
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

    public function batchSize(): int
    {
        return 1000;
    }

    // private function getRam($ram){        
    //     $ram = explode('GB', strtoupper($ram))[0];
    //     return $ram . 'DDR';
    // }

    // private function getRamType($ram){
    //     return explode(strtoupper($ram) , 'GB')[1];        
    // }

    // private function getHardDiskStorage($hdd){
    //     $hdd = strtoupper($hdd);
    //     $hdd = explode(strtoupper($hdd),'X')[1];
    //     return strpos($hdd, 'GB') ? explode($hdd,'GB')[0] . 'GB' : strpos($hdd, 'TB') ? explode($hdd,'TB')[0] . 'TB' : explode($hdd,'MB')[0] . 'MB';
    // }

    // private function getHardDiskAmount($hdd){
    //     return explode(strtoupper($hdd),'X')[0];
    // }

    // private function getHardDiskType($hdd){
    //     $hdd = $this->getHardDiskStorage($hdd);
    //     $diskType = substr($hdd, -2, -1);
    //     return explode($hdd, $diskType)[1];
    // }

    // private function getPrice($price){
    //     return floatval($price);
    // }

}
