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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('tp_name', 250)->nullable();
            $table->string('tp_type', 250)->nullable();
            $table->string('tp_TIN', 250)->nullable();
            $table->string('tp_trade_number', 250)->nullable();
            $table->string('tp_postal_number', 250)->nullable();
            $table->string('tp_phone_number', 250)->nullable();
            $table->string('tp_address_privonce', 250)->nullable();
            $table->string('tp_address_avenue', 250)->nullable();
            $table->string('tp_address_quartier', 250)->nullable();
            $table->string('tp_address_commune', 250)->nullable();
            $table->string('tp_address_rue', 250)->nullable();
            $table->string('tp_address_number', 250)->nullable();
            $table->string('vat_taxpayer', 250)->nullable();
            $table->string('ct_taxpayer', 250)->nullable();
            $table->string('tl_taxpayer', 250)->nullable();
            $table->string('tp_fiscal_center', 250)->nullable();
            $table->string('tp_activity_sector', 250)->nullable();
            $table->string('tp_legal_form', 250)->nullable();
            $table->string('payment_type', 250)->nullable();
            $table->boolean('is_actif');
            $table->integer('user_id');
            $table->string('tp_email', 250)->nullable();
            $table->string('tp_website', 250)->nullable();
            $table->string('tp_logo', 250)->nullable();
            $table->string('tp_bank', 250)->nullable();
            $table->string('tp_account_number', 250)->nullable();
            $table->string('tp_facebook', 250)->nullable();
            $table->string('tp_twitter', 250)->nullable();
            $table->string('tp_instagram', 250)->nullable();
            $table->string('tp_youtube', 250)->nullable();
            $table->string('tp_whatsapp', 250)->nullable();
            $table->string('tp_address', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
