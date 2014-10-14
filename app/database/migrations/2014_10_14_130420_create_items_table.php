<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('items', function($table){
			$table->increments('id');
			$table->string('name');
			$table->string('category');
			$table->integer('supplier_id');
			$table->string('item_number');
			$table->string('description');
			$table->decimal('cost_price',15,2);
			$table->decimal('unit_price',15,2);
			$table->decimal('quantity',15,2);
			$table->decimal('reorder_level',15,2);
			$table->tinyInteger('is_serialized');
			$table->integer('deleted')->default(0);
			$table->string('custom1',25);
			$table->string('custom2',25);
			$table->string('custom3',25);
			$table->string('custom4',25);
			$table->string('custom5',25);
			$table->string('custom6',25);
			$table->string('custom7',25);
			$table->string('custom8',25);
			$table->string('custom9',25);
			$table->string('custom10',25);
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
		Schema::drop('items');
	}

}
