<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreOrdersItemsTaxesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('store_orders_items_taxes', function($table){
			$table->increments('id');
			$table->integer('store_order_id');
			$table->integer('item_id');
			$table->integer('line');
			$table->string('name');
			$table->decimal('percent',15,2);
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
		Schema::drop('store_orders_items_taxes');
	}

}
