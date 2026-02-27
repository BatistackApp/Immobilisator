<?php

use App\Models\Asset;
use App\Models\Provider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Provider::class)->constrained();
            $table->decimal('principal_amount', 15);
            $table->decimal('interest_rate', 5);
            $table->integer('duration_months');
            $table->date('first_installment_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
