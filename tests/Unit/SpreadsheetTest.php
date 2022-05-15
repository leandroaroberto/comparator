<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\SpreadsheetController;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class SpreadsheetTest extends TestCase
{

    public $spreadSheet;

    protected function setUp(): void
    {
        $this->spreadSheet = new SpreadsheetController();
    }
   
    public function test_getRam()
    {
        $this->assertEquals('64GB', $this->spreadSheet->getRam('64GBDDR3'));
        $this->assertEquals('64GB', $this->spreadSheet->getRam('64GBDDR4'));
        $this->assertEquals('128GB', $this->spreadSheet->getRam('128GBDDR3'));
        $this->assertEquals('128GB', $this->spreadSheet->getRam('128GBDDR4'));
    }
   
    public function test_getRamType()
    {
        $this->assertEquals('DDR4', $this->spreadSheet->getRamType('64GBDDR4'));
        $this->assertEquals('DDR3', $this->spreadSheet->getRamType('64GBDDR3'));
        $this->assertEquals('DDR4', $this->spreadSheet->getRamType('128GBDDR4'));
        $this->assertEquals('DDR3', $this->spreadSheet->getRamType('128GBDDR3'));
    }
   
    public function test_getHardDiskStorage()
    {
        $this->assertEquals('1TB', $this->spreadSheet->getHardDiskStorage('4x1TBSATA2'));
        $this->assertEquals('3TB', $this->spreadSheet->getHardDiskStorage('8x3TBSATA2'));
        $this->assertEquals('300GB', $this->spreadSheet->getHardDiskStorage('5x300GBSAS'));
        $this->assertEquals('120GB', $this->spreadSheet->getHardDiskStorage('1x120GBSSD'));
        $this->assertEquals('700MB', $this->spreadSheet->getHardDiskStorage('2x700MBHDD'));
    }

    public function test_getHardDiskAmount()
    {
        $this->assertEquals('4', $this->spreadSheet->getHardDiskAmount('4x1TBSATA2'));
        $this->assertEquals('8', $this->spreadSheet->getHardDiskAmount('8x3TBSATA2'));
        $this->assertEquals('5', $this->spreadSheet->getHardDiskAmount('5x300GBSAS'));
        $this->assertEquals('1', $this->spreadSheet->getHardDiskAmount('1x120GBSSD'));
        $this->assertEquals('2', $this->spreadSheet->getHardDiskAmount('2x700MBHDD'));
    }
   
    public function test_getHardDiskType()
    {
        $this->assertEquals('SATA2', $this->spreadSheet->getHardDiskType('4x1TBSATA2'));
        $this->assertEquals('SATA2', $this->spreadSheet->getHardDiskType('8x3TBSATA2'));
        $this->assertEquals('SAS', $this->spreadSheet->getHardDiskType('5x300GBSAS'));
        $this->assertEquals('SSD', $this->spreadSheet->getHardDiskType('1x120GBSSD'));
        $this->assertEquals('HDD', $this->spreadSheet->getHardDiskType('2x700MBHDD'));
    }

}
