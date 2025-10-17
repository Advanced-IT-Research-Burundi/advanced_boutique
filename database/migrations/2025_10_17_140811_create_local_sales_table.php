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
        Schema::create('local_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('stock_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->double('total_amount');
            $table->double('paid_amount');
            $table->double('due_amount');
            $table->dateTime('sale_date');
            $table->string('type_facture')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_sales');
    }
};
