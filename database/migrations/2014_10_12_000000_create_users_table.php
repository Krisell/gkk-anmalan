<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('licence_number')->nullable();
            $table->string('gender')->nullable();
            $table->string('weight_class')->nullable();
            $table->string('role')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedInteger('granted_by')->default(0)->index();
            $table->string('password');
            $table->unsignedInteger('visits')->default(0);
            $table->timestamp('last_visited_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
