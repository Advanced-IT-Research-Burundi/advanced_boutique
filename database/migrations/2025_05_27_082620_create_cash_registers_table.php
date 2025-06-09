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

        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('stock_id')->nullable()->constrained();
            $table->double('opening_balance');
            $table->double('closing_balance');
            $table->string('status')->default('open'); // 'open', 'closed', 'suspended'
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('cash_registers');
    }
};
