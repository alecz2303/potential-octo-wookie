<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesItemsTaxesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_items_taxes', function($table){
			$table->increments('id');
			$table->integer('sale_id');
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
		Schema::drop('sales_items_taxes');
	}

}
