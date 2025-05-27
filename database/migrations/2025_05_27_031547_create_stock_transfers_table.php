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
            $table->foreignId('user_id')->constrained();
            $table->dateTime('transfer_date');
            $table->text('note');
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
