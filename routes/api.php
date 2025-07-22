<?php

use App\Http\Controllers\Api\AgencyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CashRegisterController;
use App\Http\Controllers\Api\CashTransactionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpenseTypeController;
use App\Http\Controllers\Api\ParametrageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProformaController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserStockController;
use App\Http\Controllers\Api\VehiculeController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\StockProductController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/profil',[AuthController::class, 'profil']);
    Route::post('/updatephoto',[AuthController::class, 'updatephoto']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('/reports', [ReportsController::class, 'index']);
    Route::get('/reports/export', [ReportsController::class, 'export']);

    Route::apiResource('products', ProductController::class);

    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');

    Route::prefix('sales')->group(function () {
        Route::get('/create-data', [SalesController::class, 'getCreateData']);
        Route::get('/categories/{stockId}', [SalesController::class, 'getCategories']);
        Route::get('/clients/search', [SalesController::class, 'searchClients']);
        Route::get('/products/search', [SalesController::class, 'searchProducts']);
        Route::get('/products/{productId}/stock', [SalesController::class, 'getProductStock']);
        Route::post('/store', [SalesController::class, 'store']);
    });
    Route::prefix('stock-transfers')->group(function () {
        Route::get('/stocks', [StockTransferController::class, 'getStocks']);
        Route::get('/stocks/{id}/categories', [StockTransferController::class, 'getStockCategories']);
        Route::get('/stocks/products', [StockTransferController::class, 'getProducts']);
        Route::post('/stocks/transfer', [StockTransferController::class, 'transfer']);

    });
    Route::prefix('stock-products')->group(function () {
        Route::get('/', [StockProductController::class, 'getStockProducts']);
        Route::post('/', [StockProductController::class, 'addProduct']);
        Route::delete('{id}', [StockProductController::class, 'removeProduct']);
        Route::put('{id}/quantity', [StockProductController::class, 'updateQuantity']);
        Route::post('/bulk', [StockProductController::class, 'addBulkProducts']);
        Route::get('available', [StockProductController::class, 'getAvailableProducts']);
    });
    Route::get('stock-movements/{stockProduct}', [StockMovementController::class, 'show']);

    Route::resource('stocks', StockController::class);
    Route::delete('/stocks/{stockId}/users/{userId}', [StockShowController::class, 'detachUser']);
    Route::get('stocks/{id}/export/excel', [StockProductController::class, 'exportToExcel']);
    Route::get('stocks/{id}/export/pdf', [StockProductController::class, 'exportToPdf']);


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
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/pdf', [SaleController::class, 'downloadPDF'])->name('sales.pdf');
    Route::resource('cash-registers', CashRegisterController::class);
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
    Route::delete('products/mul_destroy', [ProductController::class, 'multDestroy']);
});
