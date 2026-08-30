<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            /*
            | 1 = lundi
            | 7 = dimanche
            */
            $table->unsignedTinyInteger('day_of_week');

            $table->time('expected_entry');

            $table->time('expected_exit');

            /*
            | Tolérance avant détection d'anomalie
            */
            $table->unsignedSmallInteger('entry_tolerance_minutes')
                ->default(15);

            $table->unsignedSmallInteger('exit_tolerance_minutes')
                ->default(15);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique([
                'employee_id',
                'day_of_week'
            ]);

            $table->index('day_of_week');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};