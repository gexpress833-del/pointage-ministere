<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bureaux', function (Blueprint $table) {
            $table->foreign('chef_bureau_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bureaux', function (Blueprint $table) {
            $table->dropForeign(['chef_bureau_id']);
        });
    }
};
