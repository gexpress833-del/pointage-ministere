<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nom')->nullable()->after('id');
            $table->string('matricule')->unique()->nullable()->after('nom');
            $table->string('telephone')->nullable()->after('email');
            $table->string('photo_reference')->nullable()->after('telephone');
            $table->foreignId('bureau_id')->nullable()->after('photo_reference')->constrained('bureaux')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->after('bureau_id')->constrained('services')->nullOnDelete();
            $table->string('role')->default('agent')->after('service_id'); // administrateur, coordinateur, chef_bureau, agent
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bureau_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['nom', 'matricule', 'telephone', 'photo_reference', 'bureau_id', 'service_id', 'role']);
        });
    }
};
