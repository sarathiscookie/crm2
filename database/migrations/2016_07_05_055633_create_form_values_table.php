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
            $table->text('value');
            $table->integer('parent_id')->comment('id of customers and events');
            $table->timestamps();

            $table->foreign('form_field_id')->references('id')->on('form_fields');
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
