<?php

use App\Models\Asset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('revaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Asset::class)->constrained()->cascadeOnDelete();

            $table->date('revaluation_date');
            $table->decimal('fair_value', 15)->comment('Nouvelle valeur d\'expertise');
            $table->decimal('previous_vnc', 15)->comment('VNC au moment de la réévaluation');
            $table->decimal('gap_amount', 15)->comment('Écart de réévaluation');

            $table->string('expert_name')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // On ajoute un champ pour tracer la dernière réévaluation sur l'actif
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('revaluation_surplus', 15, 2)->default(0)->after('depreciable_basis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revaluations');

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('revaluation_surplus');
        });
    }
};
