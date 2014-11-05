<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutocompleteSalesView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$sql  = 'CREATE VIEW autocomplete_sales AS SELECT';
		$sql .= ' "Item" as Tipo,items.id as id,items.name as name,items.description as description,items.item_number as item_number';
		$sql .= ' FROM items WHERE ("items.deleted" = 0)';
		$sql .= ' UNION ALL SELECT';
		$sql .= ' "Kit" as Tipo,items_kits.id as id,items_kits.name as name,items_kits.description as description,"" as item_number';
		$sql .= ' FROM items_kits';

		DB::statement($sql);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement( 'DROP VIEW autocomplete_sales' );
	}

}
