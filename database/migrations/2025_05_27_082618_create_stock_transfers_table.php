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

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_stock_id')->constrained('stocks');
            $table->foreignId('to_stock_id')->constrained('stocks');
            //$table->foreignId('stock_product_id')->constrained('stock_products');
            // procuct id
            $table->foreignId('product_id')->constrained('products');
            $table->text('product_name')->nullable();
            $table->double('quantity')->default(0);
            $table->double('price')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->dateTime('transfer_date');
            $table->text('note')->nullable();
            $table->text('product_code')->nullable();
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users');
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
        Schema::dropIfExists('stock_transfers');
    }
};
