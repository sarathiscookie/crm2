<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unique();
            $table->integer('parent_customer_id')->unsigned();
            $table->integer('car_id')->unsigned();
            $table->integer('stage');
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('street', 255);
            $table->string('postal', 10);
            $table->string('city', 255);
            $table->string('country', 255);
            $table->enum('payment', ['creditcard', 'bank', 'cash', 'invoice']);
            $table->string('email', 100)->unique();
            $table->string('phone', 30);
            $table->string('license_plate', 50);
            $table->string('chassis_number', 100);
            $table->integer('mileage');
            $table->enum('tuning', ['yes', 'no']);
            $table->enum('dyno', ['yes', 'no']);
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
        Schema::drop('customers');
    }
}
