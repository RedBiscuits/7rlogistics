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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->double('weight', 8, 2);
            $table->double('length', 8, 2);
            $table->double('width', 8, 2);
            $table->double('height', 8, 2);
            $table->integer('quantity');
            $table->string('type');
            $table->string('description')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
