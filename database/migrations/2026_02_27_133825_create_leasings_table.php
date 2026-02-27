<?php

use App\Models\Asset;
use App\Models\Provider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leasings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class);
            $table->foreignIdFor(Provider::class);
            $table->string('contract_number');
            $table->decimal('monthly_rent');
            $table->decimal('purchase_option_price');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('option_exercised');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leasings');
    }
};
