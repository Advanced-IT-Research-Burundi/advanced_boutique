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
        Schema::create('credit_tva_details', function (Blueprint $table) {
            $table->id();
            $table->integer('credit_tva_id')->nullable();
            $table->string('type')->enum('ADD', 'SUB')->nullable();
            $table->double('montant', 64,4)->nullable();
            $table->integer('sale_id')->nullable();
            $table->string('description')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_tva_details');
    }
};