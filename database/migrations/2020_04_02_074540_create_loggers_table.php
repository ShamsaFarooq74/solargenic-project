<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loggers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('plant_id')->nullable();
            $table->string('communication_method','100');
            $table->integer('max_devices')->default('1');
            $table->string('logger_ops_mode','50');
            $table->string('heartbeat_freq','50');
            $table->string('uploading_freq','50');
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
        Schema::dropIfExists('loggers');
    }
}
