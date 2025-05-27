<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        // trancate database
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \DB::statement('TRUNCATE TABLE users');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        \App\Models\User::factory()->create([
            'name' => 'Jean Lionel',
            'email' => 'jeanlionel@gmail.com',
            'password' => Hash::make('password'),
        ]);

        \App\Models\Company::factory(1)->create();
        \App\Models\Stock::factory(1)->create();
        \App\Models\Product::factory(200)->create();
        \App\Models\Client::factory(10)->create();
        \App\Models\Supplier::factory(10)->create();
        \App\Models\Purchase::factory(1)->create();
        \App\Models\PurchaseItem::factory(1)->create();
        \App\Models\Sale::factory(1)->create();
        \App\Models\SaleItem::factory(1)->create();
        \App\Models\Payment::factory(1)->create();
        \App\Models\StockTransfer::factory(1)->create();
        \App\Models\StockTransferItem::factory(1)->create();
        \App\Models\CashRegister::factory(1)->create();
        \App\Models\CashTransaction::factory(1)->create();
        \App\Models\Expense::factory(1)->create();
        \App\Models\ExpenseType::factory(1)->create();
    }
}
