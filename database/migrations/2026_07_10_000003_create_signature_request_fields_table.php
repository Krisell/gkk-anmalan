<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_request_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signature_request_id')->constrained()->cascadeOnDelete();
            // Explicit name — the auto-generated one hits MySQL's 64-char
            // identifier limit exactly once the production table prefix is added.
            $table->foreignId('signature_request_signer_id')
                ->constrained(indexName: 'signature_request_fields_signer_foreign')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('page_index');
            $table->decimal('x', 8, 2);
            $table->decimal('y', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('height', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_request_fields');
    }
};
