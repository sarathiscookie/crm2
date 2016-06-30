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
            $table->integer('erp_id')->unique();
            $table->integer('advertiser_id')->unsigned()->nullable();
            $table->integer('shop_id')->nullable();
            $table->string('company',100)->nullable();
            $table->tinyInteger('title');
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('email', 255)->unique();
            $table->string('additional_address',100)->nullable();
            $table->string('street', 255)->nullable();
            $table->string('postal', 10)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('country_long', 255)->nullable();
            $table->string('country_short', 10)->nullable();
            $table->string('country_actindo', 10)->nullable();
            $table->string('phone_1', 30);
            $table->string('phone_2', 30);
            $table->string('phone_mobile', 30);
            $table->text('freetext')->nullable();
            $table->enum('status', ['customer','prospect','vip','reseller','blocked','deleted'])->default('customer');
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
