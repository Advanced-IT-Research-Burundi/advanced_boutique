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
        \DB::statement('TRUNCATE TABLE agencies');
        \DB::statement('TRUNCATE TABLE stocks');
        \DB::statement('TRUNCATE TABLE stock_products');
        \DB::statement('TRUNCATE TABLE products');
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        \DB::beginTransaction();

         $this->call([
            CompanySeeder::class,
        ]);


        $user = new User();
        $user->id = 1;
        $user->last_name = 'UBWIZA ';
        $user->first_name = 'BURUNDI';
        $user->role = 'admin';
        $user->email = 'ubwizaburundi@gmail.com';
        $user->password = Hash::make('password');


        $user2 = new User();
        $user2->id = 2;
        $user2->last_name = 'JEAN LIONEL';
        $user2->first_name = 'NININAHAZWE';
        $user2->role = 'admin';
        $user2->email = 'nijeanlionel@gmail.com';
        $user2->password = Hash::make('password');

        $user->save();
        $user2->save();


        $agency = \App\Models\Agency::create([
            'company_id' => 1,
            'code' => 'AGC001',
            'name' => 'Agence Principale',
            'adresse' => 'Avenue de la Croix Rouge , N° 3688',
            'manager_id' => $user->id,
            'user_id' => $user->id,
            'is_main_office' => true,
            'created_by' => $user->id,
        ]);

        $user->agency_id = $agency->id;
        $user2->agency_id = $agency->id;

        $user->save();
        $user2->save();

        $stock = \App\Models\Stock::create([
            'name' => 'Stock Principal',
            'location' => 'Avenue de la Croix Rouge , N° 3688',
            'description' => 'Stock Principal',
            'agency_id' => $agency->id,
            'created_by' => $user->id,
            'user_id' => $user->id,
        ]);

        $userstocks = \App\Models\UserStock::create([
                'user_id'=>$user->id,
                'stock_id'=>$stock->id,
                'agency_id'=>$agency->id,
                'created_by'=>$user->id
        ]);
        $userstocks = \App\Models\UserStock::create([
                'user_id'=>$user2->id,
                'stock_id'=>$stock->id,
                'agency_id'=>$agency->id,
                'created_by'=>$user->id
        ]);
        $caisse = \App\Models\CashRegister::create([
                'user_id' => $user->id,
                'stock_id' => $stock->id,
                'opening_balance' => 0,
                'closing_balance' => 0,
                'opened_at' => now(),
                'created_by' => $user->id
        ]);



        $sql = \File::get(public_path('product.sql'));
        \DB::unprepared($sql);

        \DB::commit();

    }
}
