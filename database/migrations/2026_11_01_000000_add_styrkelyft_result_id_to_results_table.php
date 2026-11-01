<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The result on data.styrkelyft.se a club record was imported from (see gkk:import-club-records), so that it is
     * never imported twice and the slideshow knows it is the registered record. Null for records entered by hand.
     */
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedBigInteger('styrkelyft_result_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn('styrkelyft_result_id');
        });
    }
};
