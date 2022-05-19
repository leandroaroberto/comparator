<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpreadsheetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResourceController;

//dashboard
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});
Route::post('upload', [SpreadsheetController::class, 'upload'])->name('upload');

//User'spage
Route::get('list', [HomeController::class, 'listAll'])->name('listall');
Route::get('list/{id}', [HomeController::class, 'showById'])->name('showById');
Route::post('search', [HomeController::class, 'searchByParams'])->name('search');

//Resources
Route::get('resource/ram', [ResourceController::class, 'getRam'])->name('get-ram');
Route::get('resource/hdd-type', [ResourceController::class, 'getHddType'])->name('get-hdd-type');
Route::get('resource/location', [ResourceController::class, 'getLocation'])->name('get-location');