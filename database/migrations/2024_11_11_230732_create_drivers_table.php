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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->unique();
            $table->string('id_number')->nullable();
            $table->date('id_expire_date')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_expire_date')->nullable();
            $table->string('car_type')->nullable();
            $table->string('car_license')->nullable();
            $table->date('car_license_expire_date')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('contact_number_2')->nullable();
            $table->string('contact_number_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
