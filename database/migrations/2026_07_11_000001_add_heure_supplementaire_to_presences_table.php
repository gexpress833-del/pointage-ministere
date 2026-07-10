<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->time('heure_supplementaire')->nullable()->after('heure_depart');
            $table->string('type_depart')->nullable()->after('heure_supplementaire');
        });
    }

    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropColumn(['heure_supplementaire', 'type_depart']);
        });
    }
};
