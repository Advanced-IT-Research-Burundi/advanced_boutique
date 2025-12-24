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
        Schema::create('credit_tvas', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->double('montant', 64,4)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_actif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_tvas');
    }
};