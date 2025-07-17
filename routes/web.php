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
    return redirect('dashboard');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/update-photo', [ProfileController::class, 'updatephoto'])->name('profile.update-photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('parametres', [App\Http\Controllers\ParametrageController::class, 'index'])->name('parametres');
    Route::put('/parametrage/company/update', [App\Http\Controllers\ParametrageController::class, 'updateCompany'])->name('parametrage.company.update');
    Route::get('stocks/{stock}/show', [App\Http\Controllers\StockController::class, 'list'])->name('stocks.list');

    Route::get('stocks/{stock}/export/pdf', [App\Http\Controllers\StockController::class, 'exportToPdf'])
    ->name('stock.export.pdf');
    Route::get('stocks/{stock}/mouvement', [App\Http\Controllers\StockController::class, 'mouvement'])->name('stocks.mouvement');
    Route::get('stocks/transfer', [App\Http\Controllers\StockController::class, 'transfer'])->name('stocks.transfer');
    Route::resource('companies', App\Http\Controllers\CompanyController::class);
    Route::resource('agencies', App\Http\Controllers\AgencyController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('stocks', App\Http\Controllers\StockController::class);
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    Route::resource('purchases', App\Http\Controllers\PurchaseController::class);
    Route::get('/purchases/{purchase}/print', [App\Http\Controllers\PurchaseController::class, 'print'])->name('purchases.print');
    Route::resource('sales', App\Http\Controllers\SaleController::class);
    Route::get('/sales/{sale}/pdf', [App\Http\Controllers\SaleController::class, 'downloadPDF'])->name('sales.pdf');
    Route::resource('cash-registers', App\Http\Controllers\CashRegisterController::class);
    ;
    Route::post('cash-register/{cashRegister}/close', [App\Http\Controllers\CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::resource('cash-transactions', App\Http\Controllers\CashTransactionController::class);
    Route::get('cash-transactions/{cashRegister}/export', [App\Http\Controllers\CashTransactionController::class, 'export'])->name('cash-transactions.export');
    Route::post('cash-transactions/{transaction}/cancel', [App\Http\Controllers\CashTransactionController::class, 'cancel'])->name('cash-transactions.cancel');
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
    Route::resource('expense-types', App\Http\Controllers\ExpenseTypeController::class);
    Route::resource('stock-transfers', App\Http\Controllers\StockTransferController::class);
    Route::resource('payments', App\Http\Controllers\PaymentController::class);
    Route::resource('user-stocks', App\Http\Controllers\UserStockController::class);
    Route::resource('vehicules', App\Http\Controllers\VehiculeController::class);

    Route::get("entre_multiple/{stock}", [App\Http\Controllers\StockController::class, "entreMultiple"])->name("entre_multiple");


});
Route::middleware(['auth'])->group(function () {
    // Gestion des stocks pour les utilisateurs
    Route::prefix('users/{user}/stocks')->name('users.stocks.')->group(function () {
        Route::get('/manage', [\App\Http\Controllers\UserStockController::class, 'manage'])->name('manage');
        Route::post('/attach', [\App\Http\Controllers\UserStockController::class, 'attach'])->name('attach');
        Route::delete('/{stock}/detach', [\App\Http\Controllers\UserStockController::class, 'detach'])->name('detach');
        Route::delete('/detach-all', [\App\Http\Controllers\UserStockController::class, 'detachAll'])->name('detach-all');
        Route::get('/history', [\App\Http\Controllers\UserStockController::class, 'history'])->name('history');
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/', [\App\Http\Controllers\UserStockController::class, 'getUserStocks'])->name('get');
            Route::post('/attach', [\App\Http\Controllers\UserStockController::class, 'attachAjax'])->name('attach');
            Route::delete('/detach', [\App\Http\Controllers\UserStockController::class, 'detachAjax'])->name('detach');
        });
    });
    Route::resource('proformas', App\Http\Controllers\ProformaController::class);
    Route::get('proformas/{proforma}/validate', [App\Http\Controllers\ProformaController::class, 'validateProforma'])->name('proformas.validate');

});


require __DIR__.'/auth.php';



Route::get('/export/excel/{token}', [App\Http\Controllers\ExportController::class, 'exportExcel'])->name('export.excel');
