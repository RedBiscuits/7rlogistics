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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->string('receiver_address');
            $table->string('awb');

            $table->foreignId('from_city')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('from_area')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('to_city')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('to_area')->nullable()->constrained('areas')->nullOnDelete();
            $table->dateTime('pickup_date')->default(now()->addDays(14));

            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            $table->tinyInteger('status')->default(0);

            $table->double('price');

            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('truck_id')->nullable()->constrained('trucks')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
