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
        Schema::create('commande_details', function (Blueprint $table) {
            $table->id();
            $table->integer('commande_id');
            $table->string('product_code')->nullable();
            $table->string('item_name')->nullable();
            $table->string('product_name')->nullable();
            $table->string('company_code')->nullable();
            $table->double('quantity')->nullable();
            $table->double('weight_kg')->nullable();
            $table->double('total_weight')->nullable();
            $table->double('prix_achat')->nullable();
            $table->double('prix_vente')->nullable();
            $table->float('remise')->nullable()->nullable();
            $table->date('date_livraison')->nullable();
            $table->string('statut')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_details');
    }
};
