<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvertersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inverters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('plant_id')->nullable();
            $table->string('serial_no');
            $table->string('model');
            $table->string('ac_output_power')->nullable();
            $table->string('daily_generation')->nullable();
            $table->string('monthly_generation')->nullable();
            $table->string('annual_generation')->nullable();
            $table->string('total_generation')->nullable();
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
        Schema::dropIfExists('inverters');
    }
}
