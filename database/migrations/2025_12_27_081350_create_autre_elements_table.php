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
        Schema::create('autre_elements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('libelle');
            $table->string('emplacement')->nullable();
            $table->double('quantite', 64, 2)->default(1);
            $table->double('valeur', 64, 2);
            $table->string('devise', 10)->default('FBU');
            $table->double('exchange_rate', 64, 2)->default(1);
            $table->enum('type_element', ["caisse","banque","avance","credit","investissement","immobilisation","autre"])->default('autre');
            $table->string('reference', 100)->nullable();
            $table->text('observation')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autre_elements');
    }
};
