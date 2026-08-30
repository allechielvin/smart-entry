<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // hire_date est déjà absente de la table employees.
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('hire_date')->nullable();
        });
    }
};