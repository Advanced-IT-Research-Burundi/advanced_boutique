<?php

use App\Http\Controllers\AgencyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ParametrageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserStockController;
use App\Http\Controllers\VehiculeController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
    // Autres routes protégées...
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('products', ProductController::class);

    Route::prefix('users/{user}/stocks')->name('users.stocks.')->group(function () {
        Route::get('/manage', [UserStockController::class, 'manage']);
        Route::post('/attach', [UserStockController::class, 'attach']);
        Route::delete('/{stock}/detach', [UserStockController::class, 'detach']);
        Route::delete('/detach-all', [UserStockController::class, 'detachAll']);
        Route::get('/history', [UserStockController::class, 'history']);
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/', [UserStockController::class, 'getUserStocks'])->name('get');
            Route::post('/attach', [UserStockController::class, 'attachAjax'])->name('attach');
            Route::delete('/detach', [UserStockController::class, 'detachAjax'])->name('detach');
        });
    });
    Route::resource('proformas', ProformaController::class);
    Route::get('proformas/{proforma}/validate', [ProformaController::class, 'validateProforma']);
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile/update-photo', [ProfileController::class, 'updatephoto']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    Route::get('parametres', [ParametrageController::class, 'index']);
    Route::put('/parametrage/company/update', [ParametrageController::class, 'updateCompany']);
    Route::get('stocks/{stock}/show', [StockController::class, 'list']);
    Route::get('stocks/{stock}/mouvement', [StockController::class, 'mouvement']);
    Route::get('stocks/transfer', [StockController::class, 'transfer']);
    Route::resource('companies', CompanyController::class);
    Route::resource('agencies', AgencyController::class);
    Route::resource('users', UserController::class);
    Route::resource('stocks', StockController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/pdf', [SaleController::class, 'downloadPDF'])->name('sales.pdf');
    Route::resource('cash-registers', CashRegisterController::class);
    ;
    Route::post('cash-register/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::resource('cash-transactions', CashTransactionController::class);
    Route::get('cash-transactions/{cashRegister}/export', [CashTransactionController::class, 'export'])->name('cash-transactions.export');
    Route::post('cash-transactions/{transaction}/cancel', [CashTransactionController::class, 'cancel'])->name('cash-transactions.cancel');
    Route::resource('expenses', ExpenseController::class);
    Route::resource('expense-types', ExpenseTypeController::class);
    Route::resource('stock-transfers', StockTransferController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('user-stocks', UserStockController::class);
    Route::resource('vehicules', VehiculeController::class);

});
