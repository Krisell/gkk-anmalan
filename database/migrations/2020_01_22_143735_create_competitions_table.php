<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('events');
            $table->date('date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('time')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->boolean('publish_count')->default(false);
            $table->boolean('publish_list')->default(false);
            $table->date('last_registration_at')->nullable();
            $table->string('show_status')->default('default');

            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitions');
    }
}
