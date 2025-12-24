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

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->double('purchase_price');
            $table->double('sale_price_ht')->nullable();
            $table->double('sale_price_ttc');
            $table->string('unit');
            $table->string('tva')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained();
            $table->float('alert_quantity');
            $table->string('image')->nullable();
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('user_id')->nullable();
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
        Schema::dropIfExists('products');
    }
};
