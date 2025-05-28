<?php

namespace Database\Seeders;
use App\Models\User;

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
        \DB::statement('TRUNCATE TABLE companies');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

         $this->call([
            CompanySeeder::class,
        ]);

        $user = new User();
        $user->id = 1;
        $user->last_name = 'Lionel';
        $user->first_name = 'Jean';
        $user->email = 'jeanlionel@gmail.com';
        $user->password = Hash::make('password');
        $user->save();

        $agency = \App\Models\Agency::create([
            'company_id' => 1,
            'code' => 'AGC001',
            'name' => 'Agence Principale',
            'adresse' => 'Bujumbura,Rohero , Avenue du Mwaro no 13',
            'manager_id' => $user->id,
            'user_id' => $user->id,
            'is_main_office' => true,
            'created_by' => $user->id,
        ]);

        $user->agency_id = $agency->id;
        $user->save();


        // \App\Models\Company::factory(1)->create();
        // \App\Models\Stock::factory(1)->create();
        // \App\Models\Product::factory(200)->create();
        // \App\Models\Client::factory(10)->create();
        // \App\Models\Supplier::factory(10)->create();
        // \App\Models\Purchase::factory(1)->create();
        // // \App\Models\PurchaseItem::factory(1)->create();
        // // \App\Models\Sale::factory(1)->create();
        // // \App\Models\SaleItem::factory(1)->create();
        // // \App\Models\Payment::factory(1)->create();
        // // \App\Models\StockTransfer::factory(1)->create();
        // // \App\Models\StockTransferItem::factory(1)->create();
        // // \App\Models\CashRegister::factory(1)->create();
        // // \App\Models\CashTransaction::factory(1)->create();
        // // \App\Models\Expense::factory(1)->create();
        // // \App\Models\ExpenseType::factory(1)->create();
    }
}
