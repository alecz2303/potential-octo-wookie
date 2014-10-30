<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceivingsPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('receivings_payments', function($table){
			$table->increments('id');
			$table->integer('receivings_id');
			$table->string('payment_type',40);
			$table->decimal('payment_amount',15,2);
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
		Schema::drop('receivings_payments');
	}

}
