<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('vat_number')->nullable();
            $table->string('address');
            $table->string('postal_code', 5);
            $table->string('city');
            $table->string('country');
            $table->integer('fiscal_year_start_month')->default(1)->comment('Mois de début d\'exercice (1-12)');
            $table->json('amortization_options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
