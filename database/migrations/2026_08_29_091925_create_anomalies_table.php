<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();

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

            $table->foreignId('movement_id')
                ->nullable()
                ->constrained('movements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->enum('type', [
                'late_entry',
                'early_exit',
                'unexpected_entry',
                'unexpected_exit',
                'long_presence',
                'duplicate_scan',
                'invalid_qr',
                'suspicious_activity',
                'other'
            ]);

            $table->string('title', 150);

            $table->text('description');

            /*
            | Niveau de gravité
            */
            $table->enum('severity', [
                'low',
                'medium',
                'high',
                'critical'
            ])->default('low');

            /*
            | Score calculé par notre système
            */
            $table->unsignedTinyInteger('score')
                ->default(0);

            $table->enum('status', [
                'new',
                'reviewing',
                'resolved',
                'ignored'
            ])->default('new');

            $table->timestamp('detected_at');

            $table->timestamp('resolved_at')->nullable();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('resolution_note')->nullable();

            $table->timestamps();

            $table->index('type');
            $table->index('severity');
            $table->index('status');
            $table->index('detected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};