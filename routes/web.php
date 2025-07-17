<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $base_url = \URL::to('/');
    return redirect('' . $base_url . '/docs/api#/');
});



require __DIR__.'/auth.php';



Route::get('/export/excel/{token}', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('export.excel');
