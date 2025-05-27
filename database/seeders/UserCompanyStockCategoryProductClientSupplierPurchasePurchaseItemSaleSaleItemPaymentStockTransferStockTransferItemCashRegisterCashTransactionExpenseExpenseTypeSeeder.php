<?php

namespace Database\Seeders;

use App\Models\UserCompanyStockCategoryProductClientSupplierPurchasePurchaseItemSaleSaleItemPaymentStockTransferStockTransferItemCashRegisterCashTransactionExpenseExpenseType;
use Illuminate\Database\Seeder;

class UserCompanyStockCategoryProductClientSupplierPurchasePurchaseItemSaleSaleItemPaymentStockTransferStockTransferItemCashRegisterCashTransactionExpenseExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserCompanyStockCategoryProductClientSupplierPurchasePurchaseItemSaleSaleItemPaymentStockTransferStockTransferItemCashRegisterCashTransactionExpenseExpenseType::factory()->count(5)->create();
    }
}
