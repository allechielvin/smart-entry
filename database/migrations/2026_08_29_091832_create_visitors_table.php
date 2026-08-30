<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();

            $table->string('company', 150)->nullable();

            $table->string('id_type', 50)->nullable();
            $table->string('id_number', 100)->nullable();

            $table->string('photo_path')->nullable();

            $table->text('reason')->nullable();

            $table->enum('status', [
                'expected',
                'inside',
                'completed',
                'blocked'
            ])->default('expected');

            $table->timestamps();

            $table->index(['last_name', 'first_name']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};