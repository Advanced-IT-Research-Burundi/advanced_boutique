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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::resource('companies', App\Http\Controllers\CompanyController::class);

Route::resource('stocks', App\Http\Controllers\StockController::class);

Route::resource('categories', App\Http\Controllers\CategoryController::class);

Route::resource('products', App\Http\Controllers\ProductController::class);

Route::resource('clients', App\Http\Controllers\ClientController::class);

Route::resource('suppliers', App\Http\Controllers\SupplierController::class);

Route::resource('purchases', App\Http\Controllers\PurchaseController::class);

Route::resource('sales', App\Http\Controllers\SaleController::class);

Route::resource('cash-registers', App\Http\Controllers\CashRegisterController::class);

Route::resource('cash-transactions', App\Http\Controllers\CashTransactionController::class);

Route::resource('expenses', App\Http\Controllers\ExpenseController::class);

Route::resource('expense-types', App\Http\Controllers\ExpenseTypeController::class);
