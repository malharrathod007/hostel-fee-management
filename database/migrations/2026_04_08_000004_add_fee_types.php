<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('student_rent', 10, 2)->default(0)->after('rent_per_person');
            $table->decimal('employee_rent', 10, 2)->default(0)->after('student_rent');
        });

        Schema::table('persons', function (Blueprint $table) {
            $table->enum('person_type', ['student', 'employee'])->default('student')->after('room_id');
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->enum('fee_type', ['student', 'employee'])->default('student')->after('person_id');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['student_rent', 'employee_rent']);
        });

        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn('person_type');
        });

        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('fee_type');
        });
    }
};
