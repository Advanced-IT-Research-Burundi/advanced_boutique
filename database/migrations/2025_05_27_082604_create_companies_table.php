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

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('tp_name');
            $table->string('tp_type');
            $table->string('tp_TIN');
            $table->string('tp_trade_number')->nullable();
            $table->string('tp_postal_number')->nullable();
            $table->string('tp_phone_number')->nullable();
            $table->string('tp_address_province')->nullable();
            $table->string('tp_address_commune')->nullable();
            $table->string('tp_address_quartier')->nullable();
            $table->string('tp_address_avenue')->nullable();
            $table->string('tp_address_rue')->nullable();
            $table->string('tp_address_number')->nullable();
            $table->string('vat_taxpayer')->nullable();
            $table->string('ct_taxpayer')->nullable();
            $table->string('tl_taxpayer')->nullable();
            $table->string('tp_fiscal_center')->nullable();
            $table->string('tp_activity_sector')->nullable();
            $table->string('tp_legal_form')->nullable();
            $table->string('payment_type')->nullable();
            $table->boolean('is_actif')->default(true);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
