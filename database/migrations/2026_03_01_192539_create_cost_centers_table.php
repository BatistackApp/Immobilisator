<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Ex: DIRECTION, ATELIER, BUREAU_ETUDE');
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\CostCenter::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });
    }
};
