<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('visitor_id')
                ->nullable()
                ->constrained('visitors')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            /*
            | Token sécurisé du QR Code
            */
            $table->string('token', 191)->unique();

            /*
            | Version du QR Code
            */
            $table->unsignedInteger('version')->default(1);

            $table->boolean('is_active')->default(true);

            $table->timestamp('expires_at')->nullable();

            $table->timestamp('last_used_at')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};