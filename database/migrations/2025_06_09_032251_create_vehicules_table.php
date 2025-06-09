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

        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('model')->nullable();
            $table->string('immatriculation')->nullable();
            $table->string('brand')->nullable();
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->decimal('price')->nullable();
            $table->string('status')->default('disponible');
            $table->text('description')->nullable();
            $table->foreignId('agency_id')->nullable()->constrained();
            $table->foreignId('created_by');
            $table->foreignId('user_id');
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
        Schema::dropIfExists('vehicules');
    }
};
