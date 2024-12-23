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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('fortnox_invoice_document_number')->nullable();
            $table->timestamp('fortnox_invoice_created_at')->nullable();
            $table->timestamp('fortnox_invoice_emailed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('fortnox_invoice_document_number');
            $table->dropColumn('fortnox_invoice_created_at');
            $table->dropColumn('fortnox_invoice_emailed_at');
        });
    }
};
