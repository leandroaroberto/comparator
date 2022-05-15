<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spreadsheet extends Model
{
    use HasFactory;

    protected $tabe = 'spreadsheets';

     protected $fillable = [
        'model_name',
        'ram',
        'ram_type',
        'hard_disk_storage',
        'hard_disk_amount',
        'hard_disk_type',
        'location',
        'price',
    ];
}
