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
        Schema::disableForeignKeyConstraints();

        Schema::create('depenses_importations', function (Blueprint $table) {
            $table->id();
            $table->string('depense_importation_type')->nullable();
            $table->foreignId('depense_importation_type_id')->constrained();
            $table->string('currency');
            $table->double('exchange_rate');
            $table->double('amount');
            $table->double('amount_currency');
            $table->dateTime('date');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses_importations');
    }
};
