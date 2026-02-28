<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Switch from global read_at to per-user read status.
     */
    public function up(): void
    {
        Schema::create('model_change_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_change_notification_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('read_at');
            $table->foreign('model_change_notification_id', 'mcn_reads_notif_fk')
                ->references('id')->on('model_change_notifications')->cascadeOnDelete();
            $table->foreign('user_id', 'mcn_reads_user_fk')
                ->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['model_change_notification_id', 'user_id'], 'mcn_reads_notif_user_unique');
        });

        // Migrate existing read_at data: create reads for all users who could have "read" it
        // Since we had global read, we can't know which user read it. We'll leave existing
        // read notifications as "unread" for all users (no rows in reads = unread).
        // Alternative: create reads for all admin users for notifications with read_at set?
        // Simpler: just drop read_at, all become "unread" for everyone. Users can re-mark.
        Schema::table('model_change_notifications', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('model_change_notifications', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('message');
        });

        Schema::dropIfExists('model_change_notification_reads');
    }
};
