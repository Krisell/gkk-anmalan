<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_request_signers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signature_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->uuid('token')->unique();
            $table->timestamp('signed_at')->nullable();
            $table->string('signature_type')->nullable();
            $table->mediumText('signature_png')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Explicit name — the auto-generated one exceeds MySQL's 64-char
            // identifier limit once the production table prefix is added.
            $table->unique(['signature_request_id', 'user_id'], 'signature_request_signers_request_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_request_signers');
    }
};
