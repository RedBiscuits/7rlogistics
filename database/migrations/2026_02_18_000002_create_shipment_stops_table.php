<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')
                ->constrained('shipments')
                ->onDelete('cascade');
            $table->enum('type', ['pickup', 'delivery']);
            $table->unsignedTinyInteger('sequence')->default(1);
            $table->foreignId('area_id')
                ->nullable()
                ->constrained('areas')
                ->onDelete('set null');
            $table->foreignId('city_id')
                ->nullable()
                ->constrained('cities')
                ->onDelete('set null');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'type', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_stops');
    }
};
