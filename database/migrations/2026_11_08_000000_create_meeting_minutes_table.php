<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->string('meeting_type')->default('Styrelsemöte');
            // The meeting's number within the club's year (verksamhetsåret), e.g. "Styrelsemöte 7, verksamhetsåret 2026".
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('number');
            $table->date('date');
            $table->string('start_time', 5)->nullable();
            $table->string('end_time', 5)->nullable();
            $table->string('location')->nullable();
            $table->string('chair')->nullable();
            $table->string('secretary')->nullable();
            $table->string('adjuster')->nullable();
            // [{name, present}]
            $table->json('attendees');
            // [{title, body}] — the meeting's own items, numbered after the standard opening items.
            $table->json('items');
            $table->text('other_business')->nullable();
            $table->dateTime('next_meeting_at')->nullable();
            $table->string('next_meeting_location')->nullable();
            $table->string('next_meeting_note')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            // Review by the adjuster (protokolljusteraren) and publishing to Dokument.
            $table->timestamp('review_requested_at')->nullable();
            $table->text('review_comment')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['year', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes');
    }
};
