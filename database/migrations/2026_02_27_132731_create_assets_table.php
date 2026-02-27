<?php

use App\Models\AssetCategory;
use App\Models\Location;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(AssetCategory::class)->constrained();
            $table->foreignIdFor(Location::class)->nullable()->constrained();
            $table->string('reference')->unique();
            $table->string('designation');

            // Financement & Valeurs
            $table->string('funding_type');
            $table->decimal('acquisition_value', 15, 2);
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->date('acquisition_date');
            $table->date('service_date');
            $table->integer('useful_life');

            // Fiscalité (2054/2055)
            $table->decimal('gross_value_opening', 15)->default(0);
            $table->decimal('accumulated_depreciation_opening', 15)->default(0);

            $table->string('amortization_method');
            $table->string('status');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
