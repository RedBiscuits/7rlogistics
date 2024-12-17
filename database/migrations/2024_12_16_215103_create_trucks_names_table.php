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
        // Schema::create('trucks_names', function (Blueprint $table) {
        //     $table->id();
        //     $table->string("name");
        //     $table->timestamps();
        // });

        // Schema::table('trucks', function (Blueprint $table) {
        //     $table->foreignId('name_id')->constrained('trucks_names')->cascadeOnDelete();
        // });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('trucks_names');

        // Schema::table('trucks', function (Blueprint $table) {
        //     $table->dropForeign('name_id');
        // });
    }
};
