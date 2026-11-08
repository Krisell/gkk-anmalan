<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Ordförande, Kassör, Ledamot, Suppleant …
            $table->string('role');
            $table->text('photo_url')->nullable();
            // What members can contact this person about, e.g. ["Ungdomsträning", "Klubbkläder"].
            $table->json('areas')->nullable();
            // Shown publicly on the Om GKK page.
            $table->string('email')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_members');
    }
};
