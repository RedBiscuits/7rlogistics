<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->dropUnique(['driver_id', 'date']);
        });

        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->unsignedTinyInteger('shift_number')->default(1)->after('date');
            $table->index(['driver_id', 'date', 'shift_number']);
        });
    }

    public function down(): void
    {
        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->dropIndex(['driver_id', 'date', 'shift_number']);
            $table->dropColumn('shift_number');
        });

        Schema::table('driver_attendances', function (Blueprint $table) {
            $table->unique(['driver_id', 'date']);
        });
    }
};
