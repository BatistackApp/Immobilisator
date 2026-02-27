<?php

use App\Models\Asset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amortization_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class)->constrained();
            $table->integer('year');
            $table->decimal('base_value', 15);
            $table->decimal('annuity_amount', 15);
            $table->decimal('accumulated_amount', 15);
            $table->decimal('book_value', 15);
            $table->boolean('is_posted')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amortization_lines');
    }
};
