<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_group_id')->unsigned();
            $table->string('title', 100);
            $table->string('description', 255);
            $table->string('placeholder', 100);
            $table->string('type', 50);
            $table->text('options');
            $table->string('validation', 100);
            $table->enum('relation',['customer','event']);

            $table->foreign('form_group_id')->references('id')->on('form_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('form_fields');
    }
}
