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
        Schema::create('local_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('local_sale_id')->constrained();
            $table->foreignId('product_id');
            $table->integer('quantity');
            $table->double('sale_price');
            $table->double('product_name')->nullable();
            $table->double('discount');
            $table->double('subtotal');
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_sale_items');
    }
};
