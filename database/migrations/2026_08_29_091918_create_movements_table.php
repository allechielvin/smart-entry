<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();

            /*
            | Personne concernée
            */
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('visitor_id')
                ->nullable()
                ->constrained('visitors')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            /*
            | Point d'accès
            */
            $table->foreignId('access_point_id')
                ->constrained('access_points')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            | Type de mouvement
            */
            $table->enum('type', [
                'entry',
                'exit'
            ]);

            /*
            | Méthode utilisée
            */
            $table->enum('method', [
                'qr',
                'nfc',
                'manual'
            ])->default('qr');

            /*
            | Date et heure réelles
            */
            $table->timestamp('occurred_at');

            /*
            | Informations techniques
            */
            $table->string('device_id', 100)->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->string('user_agent', 255)->nullable();

            /*
            | Localisation éventuelle
            */
            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            /*
            | Résultat de la vérification
            */
            $table->enum('verification_status', [
                'verified',
                'pending',
                'rejected'
            ])->default('verified');

            /*
            | Score d'anomalie
            | 0 = normal
            | 100 = très suspect
            */
            $table->unsignedTinyInteger('anomaly_score')
                ->default(0);

            /*
            | Commentaire éventuel
            */
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'employee_id',
                'occurred_at'
            ]);

            $table->index([
                'visitor_id',
                'occurred_at'
            ]);

            $table->index('type');

            $table->index('method');

            $table->index('verification_status');

            $table->index('anomaly_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};