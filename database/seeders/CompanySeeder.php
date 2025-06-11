<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            // Informations de base
            'tp_name' => 'UBWIZA BURUNDI BUSINESS SPRL',
            'tp_type' => 'COMMERCE',
            'tp_TIN' => '40000000000',
            'tp_trade_number' => '0000',
            'tp_phone_number' => '+257 62 XX XX XX / +257 62 XX XX XX',
            'tp_email' => 'ubwizaburundi@gmail.com',
            'tp_address_privonce' => 'BUJUMBURA-MAIRIE',
            'tp_address_commune' => 'Mukaza',
            'tp_address_quartier' => 'Rohero',
            'tp_address_avenue' => 'Avenue de la Croix Rouge , N° 3688',
            'tp_address' => 'Q. Rohero, Avenue de la Croix Rouge , N° 3688, Bujumbura, Burundi',
            'vat_taxpayer' => 'OUI',
            'ct_taxpayer' => 'OUI',
            'tl_taxpayer' => 'OUI',
            'tp_fiscal_center' => 'SPRL',

            'is_actif' => true,
            'user_id' => 1,

            'tp_whatsapp' => '+257 62 XX XX XX',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
