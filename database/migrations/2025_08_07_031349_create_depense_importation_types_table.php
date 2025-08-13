<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('depense_importation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed the table with initial data
        DB::table('depense_importation_types')->insert([
            ['name' => 'TRANSPORT', 'description' => 'Frais liés au transport des marchandises'],
            ['name' => 'DEDOUANEMENT', 'description' => 'Frais de dédouanement'],
            ['name' => 'LICENCE', 'description' => 'Droits de douane et licences'],
            ['name' => 'ASSURANCE', 'description' => 'Frais d\'assurances'],
            ['name' => 'IMPREVU', 'description' => 'Frais imprevus'],
            ['name' => 'BBN', 'description' => 'Frais liés au bon de livraison'],
            ['name' => 'DECHARGEMENT', 'description' => 'Frais de déchargement'],
            ['name' => 'PALETTES', 'description' => 'frais pour les palettes'],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depense_importation_types');
    }
};
