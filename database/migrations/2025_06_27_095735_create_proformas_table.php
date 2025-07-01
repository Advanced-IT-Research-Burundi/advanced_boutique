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

        Schema::create('proformas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->double('total_amount');
            $table->double('due_amount');
            $table->date('sale_date');
            $table->text('note')->nullable();
            $table->string('invoice_type')->nullable();
            $table->foreignId('agency_id')->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->text('proforma_items')->nullable();
            $table->text('client')->nullable();
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
        Schema::dropIfExists('proformas');
    }
};
