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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('notifiable_type'); // Event or Competition
            $table->unsignedBigInteger('notifiable_id');
            $table->unsignedBigInteger('sent_by'); // Admin user who sent the notification
            $table->integer('recipients_count')->default(0);
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
            $table->foreign('sent_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
