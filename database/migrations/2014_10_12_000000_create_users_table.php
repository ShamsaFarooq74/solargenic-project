<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name')->nullable();
            $table->string('username','30');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone',20)->nullable();
            $table->string('profile_pic',500)->nullable();
            $table->integer('company_id')->nullable();
            $table->enum('is_active', array('Y','N'))->default('Y');
            $table->enum('is_admin', array('Y','N'))->default('N');
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
