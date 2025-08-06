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
        Schema::create('product_company_names', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->nullable()->unique();
            $table->string('company_code')->nullable();
            $table->string('item_name')->nullable();
            $table->string('size')->nullable();
            $table->string('packing_details')->nullable();
            $table->string('mfg_location')->nullable();
            $table->double('weight_kg')->nullable();
            $table->double('order_qty')->nullable();
            $table->double('total_weight')->nullable();
            $table->string('pu')->nullable();
            $table->float('total_weight_pu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_company_names');
    }
};
