<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('driver_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');
            $table->foreignId('attendance_location_id')
                ->nullable()
                ->constrained('attendance_locations')
                ->onDelete('set null');
            $table->timestamp('check_in_at');
            $table->decimal('check_in_latitude', 10, 8);
            $table->decimal('check_in_longitude', 11, 8);
            $table->timestamp('check_out_at')->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable()->comment('Calculated hours worked');
            $table->date('date')->comment('Attendance date for easy querying');
            $table->timestamps();
            
            // Indexes
            $table->index('driver_id');
            $table->index('date');
            $table->index('check_in_at');
            
            // Unique constraint: one check-in per driver per day
            $table->unique(['driver_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_attendances');
    }
};
