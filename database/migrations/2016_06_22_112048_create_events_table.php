<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('customer_id')->unsigned();
            $table->integer('vehicle_id')->unsigned();
            $table->integer('partner_id');
            $table->string('title', 200);
            $table->text('freetext_external');
            $table->text('freetext_internal');
            $table->integer('stage');
            $table->integer('mileage');
            $table->enum('tuning', ['yes', 'no']);
            $table->enum('dyno', ['yes', 'no']);
            $table->enum('payment', ['creditcard', 'bank', 'paypal', 'cash', 'invoice']);
            $table->dateTime('begin_at');
            $table->dateTime('end_at')->nullable();
            $table->decimal('price', 10,2);
            $table->decimal('discount', 10,2);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');

        });
        $statement = "ALTER TABLE events AUTO_INCREMENT = 10000;";
        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('events');
    }
}
