<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            | Action réalisée
            */
            $table->string('action', 100);

            /*
            | Objet concerné
            */
            $table->string('subject_type', 100)->nullable();

            $table->unsignedBigInteger('subject_id')->nullable();

            /*
            | Anciennes / nouvelles valeurs
            */
            $table->json('old_values')->nullable();

            $table->json('new_values')->nullable();

            /*
            | Informations techniques
            */
            $table->string('ip_address', 45)->nullable();

            $table->string('user_agent', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index([
                'subject_type',
                'subject_id'
            ]);

            $table->index('action');

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};