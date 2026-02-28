<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->integer('default_useful_life')->default(0);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Provider::class)->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            $table->dropColumn('default_useful_life');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('provider_id');
        });
    }
};
