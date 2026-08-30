<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_points', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            $table->string('code', 50)->unique();

            $table->string('location', 150)->nullable();

            $table->enum('type', [
                'entrance',
                'exit',
                'both'
            ])->default('both');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_points');
    }
};