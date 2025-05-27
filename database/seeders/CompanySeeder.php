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
            'tp_name' => 'BUDENTAL SERVICES',
            'tp_type' => 'Service',
            'tp_TIN' => '40001647371',
            'tp_trade_number' => '29723/21',
            'tp_phone_number' => '+257 62 10 63 08 / +257 62 50 50 00',
            'tp_email' => 'budentalservices@gmail.com',
            'tp_address_privonce' => 'BUJUMBURA-MAIRIE',
            'tp_address_commune' => 'Mukaza',
            'tp_address_quartier' => 'Rohero',
            'tp_address_avenue' => 'N°12, Ave d\'Italie',
            'tp_address' => 'Rohero, N°12, Ave d\'Italie, Bujumbura, Burundi',
            'vat_taxpayer' => 'OUI',
            'ct_taxpayer' => 'OUI',
            'tl_taxpayer' => 'OUI',
            'tp_fiscal_center' => 'DMC',

            'is_actif' => true,
            'user_id' => 1,

            'tp_whatsapp' => '+257 62 50 50 00',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
