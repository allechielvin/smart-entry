<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {

            // Code unique du visiteur
            $table->string('visitor_code', 50)
                ->nullable()
                ->unique()
                ->after('id');

            // Photo
            $table->string('photo')
                ->nullable()
                ->after('id_number');

            // Motif de la visite
            $table->text('purpose')
                ->nullable()
                ->after('photo');

            // Employé qui reçoit le visiteur
            $table->foreignId('host_employee_id')
                ->nullable()
                ->after('purpose')
                ->constrained('employees')
                ->nullOnDelete();

            // Rendez-vous prévu
            $table->dateTime('expected_arrival')
                ->nullable()
                ->after('host_employee_id');

            $table->dateTime('expected_departure')
                ->nullable()
                ->after('expected_arrival');

            // Départ réel
            $table->dateTime('actual_departure')
                ->nullable()
                ->after('expected_departure');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {

            $table->dropForeign(['host_employee_id']);

            $table->dropColumn([
                'visitor_code',
                'photo',
                'purpose',
                'host_employee_id',
                'expected_arrival',
                'expected_departure',
                'actual_departure',
            ]);
        });
    }
};
