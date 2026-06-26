<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bureaux', function (Blueprint $table) {
            $table->id();
            $table->string('nom_bureau');
            $table->unsignedBigInteger('chef_bureau_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bureaux');
    }
};
