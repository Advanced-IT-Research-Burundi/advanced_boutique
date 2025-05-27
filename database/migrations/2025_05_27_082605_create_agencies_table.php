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

        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('adresse');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->foreignId('parent_agency_id')->nullable()->constrained('agencies');
            $table->boolean('is_main_office')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('user_id');
            $table->foreignId('agency_id');
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
        Schema::dropIfExists('agencies');
    }
};
