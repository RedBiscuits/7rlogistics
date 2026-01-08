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
        Schema::create('driver_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->constrained('drivers')
                ->onDelete('cascade');
            $table->foreignId('shipment_id')
                ->nullable()
                ->constrained('shipments')
                ->onDelete('set null');
            $table->string('title', 255);
            $table->text('body');
            $table->tinyInteger('type')->default(0)
                ->comment('0=shipment_assigned, 1=license_expiry, 2=general');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('driver_id');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_notifications');
    }
};
