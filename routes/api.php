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
use App\Http\Controllers\Api\RapportController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\LocalSaleController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserStockController;
use App\Http\Controllers\Api\VehiculeController;
use App\Http\Controllers\Api\StockProductController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\EntreMultipleController;
use App\Http\Controllers\AutreElementController;
use App\Http\Controllers\CommandeDetailsController;
use App\Http\Controllers\CommandesController;
use App\Http\Controllers\CreditTvaController;
use App\Http\Controllers\CreditTvaDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepenseImportationTypeController;
use App\Http\Controllers\DepensesImportationController;
use App\Http\Controllers\ProductCompanyNameController;
use App\Http\Controllers\ProduitsTmpController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UpdateDBController;
use App\Http\Controllers\VehiculeDepenseController;
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
    Route::apiResource('units', UnitController::class);

    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('categories.bulk-delete');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');

    Route::prefix('sales')->group(function () {
        Route::get('/', [SaleController::class, 'index']);
        Route::get('/create-data', [SaleController::class, 'getCreateData']);
        Route::get('/categories/{stockId}', [SaleController::class, 'getCategories']);
        Route::get('/clients/search', [SaleController::class, 'searchClients']);
        Route::get('/products/search', [SaleController::class, 'searchProducts']);
        Route::get('/products/{productId}/stock', [SaleController::class, 'getProductStock']);
        Route::post('/store', [SaleController::class, 'store']);
        Route::get('/{sale}', [SaleController::class, 'show']);
        Route::put('/{sale}/cancel', [SaleController::class, 'cancel']);
        Route::post('/{sale}/payment', [SaleController::class, 'payment']);
        Route::get('/{sale}/pdf', [SaleController::class, 'downloadPDF'])->name('sales.pdf');
    });

    Route::prefix('local-sales')->group(function () {
        Route::get('/', [LocalSaleController::class, 'index']);
        Route::get('/create-data', [LocalSaleController::class, 'getCreateData']);
        Route::get('/categories/{stockId}', [LocalSaleController::class, 'getCategories']);
        Route::get('/clients/search', [LocalSaleController::class, 'searchClients']);
        Route::get('/products/search', [LocalSaleController::class, 'searchProducts']);
        Route::get('/products/{productId}/stock', [LocalSaleController::class, 'getProductStock']);
        Route::post('/store', [LocalSaleController::class, 'store']);
        Route::get('/{sale}', [LocalSaleController::class, 'show']);
        Route::put('/{sale}/cancel', [LocalSaleController::class, 'cancel']);
        Route::post('/{sale}/payment', [LocalSaleController::class, 'payment']);
        Route::get('/{sale}/pdf', [LocalSaleController::class, 'downloadPDF'])->name('sales.pdf');
    });

    Route::prefix('proformas')->group(function () {
        Route::get('/create-data', [ProformaController::class, 'getCreateData']);
        Route::get('/categories/{stockId}', [ProformaController::class, 'getCategories']);
        Route::get('/clients/search', [ProformaController::class, 'searchClients']);
        Route::get('/products/search', [ProformaController::class, 'searchProducts']);
        Route::get('/products/{productId}/stock', [ProformaController::class, 'getProductStock']);
        Route::post('/store', [ProformaController::class, 'store']);
        Route::get('/products/{productId}/stock', [ProformaController::class, 'getProductStock']);
    });
    Route::prefix('stock-transfers')->group(function () {
        Route::get('/stocks', [StockTransferController::class, 'getStocks']);
        Route::get('/stocks/{id}/proformas', [StockTransferController::class, 'getStockProformas']);
        Route::get('/stocks/{id}/categories', [StockTransferController::class, 'getStockCategories']);
        Route::get('/stocks/products', [StockTransferController::class, 'getProducts']);
        Route::get('/stocks/products/proforma', [StockTransferController::class, 'getProformaProducts']);
        Route::post('/stocks/transfer', [StockTransferController::class, 'transfer']);

    });
    Route::prefix('stock-products')->group(function () {
        Route::get('/', [StockProductController::class, 'getStockProducts']);
        Route::post('/', [StockProductController::class, 'addProduct']);
        Route::delete('{id}', [StockProductController::class, 'removeProduct']);
        Route::put('{id}/quantity', [StockProductController::class, 'updateQuantity']);
        Route::post('/bulk', [StockProductController::class, 'addBulkProducts']);
        Route::get('available', [StockProductController::class, 'getAvailableProducts']);
        Route::get('for-entry', [EntreMultipleController::class, 'getProductsForEntry']);
        Route::post('bulk-entry', [EntreMultipleController::class, 'processBulkEntry']);
        Route::get('{id}', [StockMovementController::class, 'getStockProduct']);
    });
    Route::prefix('stocks/{stockId}')->group(function () {
        Route::get('categories', [EntreMultipleController::class, 'getStockCategories']);
        Route::get('entry-summary', [EntreMultipleController::class, 'getEntrySummary']);
        Route::delete('/users/{userId}', [StockShowController::class, 'detachUser']);
    });
    Route::prefix('stock-movements')->group(function () {
        Route::get('/', [StockMovementController::class, 'getMovements']);
        Route::post('/', [StockMovementController::class, 'createMovement']);
        Route::get('stats/{stockProductId}', [StockMovementController::class, 'getMovementStats']);
    });
    Route::resource('stocks', StockController::class);
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
    Route::post('proformas/{proforma}/validate', [ProformaController::class, 'validateProforma']);
    Route::post('proformas/validate/bulk', [ProformaController::class, 'validateBulkProformas']);
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile/update-photo', [ProfileController::class, 'updatephoto']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
    Route::get('parametres', [ParametrageController::class, 'index']);
    Route::put('/parametrage/company/update', [ParametrageController::class, 'updateCompany']);
    Route::get('stocks/{stock}/show', [StockController::class, 'list']);
    Route::get('stocks/{stock}/mouvement', [StockController::class, 'mouvement']);
    Route::resource('companies', CompanyController::class);
    Route::resource('agencies', AgencyController::class);
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::resource('cash-registers', CashRegisterController::class);
    Route::post('cash-register/{cashRegister}/close', [CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::post('cash-register/{cashRegister}/open', [CashRegisterController::class, 'open'])->name('cash-register.open');
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

    Route::apiResource('product-company-names',ProductCompanyNameController::class);
    Route::post('imports/company_products', [ProductCompanyNameController::class, 'importCompanyProducts']);
    Route::apiResource('commandes', CommandesController::class);
    Route::get('commande/livraison', [CommandesController::class, 'livraison']);
    Route::post('commande/livraison/valide', [CommandesController::class, 'livraisonValide']);
    Route::apiResource('commande-details', CommandeDetailsController::class);
    Route::apiResource('depense-importation-types', DepenseImportationTypeController::class);Route::apiResource('depenses-importations', DepensesImportationController::class);
    Route::get('depenses-importations/commandes/{id}', [DepensesImportationController::class, 'importationCommandes']);

    Route::get("reports/depense_annuel", [RapportController::class, 'depense_annuel']);

    Route::apiResource('vehicule-depenses', VehiculeDepenseController::class);
    Route::get('bon-entree', [CommandesController::class, 'bonEntre']);
    Route::get('/product/pdf', [ProductController::class, 'download'])->name('product.pdf');
    Route::apiResource('produits-tmps', ProduitsTmpController::class);
    Route::apiResource('credit-tvas', CreditTvaController::class);
    Route::apiResource('credit-tva-details', CreditTvaDetailController::class);
    Route::get('stock_billan', [RapportController::class, 'stock_billan']);Route::apiResource('autre-elements', AutreElementController::class);

});

Route::apiResource('invoince-pointers', App\Http\Controllers\InvoincePointerController::class);

 Route::get('update_database', [UpdateDBController::class, 'update_database'])->name('update_database');
