<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('company_id')->nullable();
            $table->string('plant_name');
            $table->string('timezone','100');
            $table->string('phone','20')->nullable();
            $table->string('currency','30')->default('PKR');
            $table->string('location','500')->nullable();
            $table->decimal('loc_lat','12','4')->nullable();
            $table->decimal('loc_long','12','4')->nullable();
            $table->string('capacity','100')->nullable();
            $table->string('benchmark_price','100')->nullable();
            $table->string('direction_angle','5')->nullable();
            $table->string('tilt_angle','5')->nullable();
            $table->string('avg_generation_price','50')->nullable();
            $table->string('national_fit','200')->nullable();
            $table->string('building_cost','200')->nullable();
            $table->string('loan_proportion','100')->nullable();
            $table->integer('plant_type_id')->nullable();
            $table->integer('system_type_id')->nullable();
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
        Schema::dropIfExists('plants');
    }
}
