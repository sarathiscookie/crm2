<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('execution_id')->nullable();
            $table->string('producer', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('motor', 100)->nullable();
            $table->integer('power')->nullable();
            $table->integer('torque')->nullable();
            $table->string('chassis_number', 100)->nullable();
            $table->string('license_plate', 50);
            $table->tinyInteger('gearbox');
            $table->text('freetext');
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
        Schema::drop('vehicles');
    }
}
