<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_field_id')->unsigned();
            $table->text('value')->nullable();
            $table->integer('parent_id')->unsigned();
            $table->timestamps();

            $table->foreign('form_field_id')->references('id')->on('form_fields');
            $table->foreign('parent_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('form_values');
    }
}
