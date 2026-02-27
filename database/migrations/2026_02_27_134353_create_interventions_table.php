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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class)->constrained();
            $table->foreignIdFor(Provider::class)->nullable()->constrained();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('cost', 15);
            $table->dateTime('intervention_date');
            $table->boolean('is_capitalized')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
