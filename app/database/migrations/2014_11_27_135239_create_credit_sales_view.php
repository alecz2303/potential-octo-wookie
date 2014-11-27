<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditSalesView extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$sql  = 'CREATE VIEW credit_sales AS SELECT';
		$sql .= '
			sales.id as sale_id,sales.customer_id,SUBSTRING(sales.created_at,1,10) as created_at,
			CONCAT(first_name," ",last_name) as full_name,0 as payment_amount,
			sum(quantity_purchased * item_unit_price) as subtotal, percent/100 * sum(quantity_purchased * item_unit_price) as tax,
			sum(quantity_purchased * item_unit_price) + (percent/100 * sum(quantity_purchased * item_unit_price)) as total,
			sum(quantity_purchased * item_unit_price) + (percent/100 * sum(quantity_purchased * item_unit_price)) as dif
		';
		$sql .= '
			FROM
				sales
			left join
				sales_items on sales_items.sale_id = sales.id
			left join
				sales_items_taxes on sales_items_taxes.sale_id = sales.id
			left join
				customers on customers.id = sales.customer_id
			left join
				peoples on peoples.id = customers.people_id
			where
				sales.id not in (select sale_id from sales_payments)
			group by
				sales.id
		';
		$sql .= ' UNION ALL SELECT';
		$sql .= '
			sales_payments.sale_id,sales.customer_id,SUBSTRING(sales.created_at,1,10) as created_at,
			CONCAT(first_name," ",last_name) as full_name,SUM(payment_amount) as payment_amount,
			(SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) as subtotal,
			(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id) as tax,
			(SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) + (SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id) as total,
			((SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) + (SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id)) - SUM(payment_amount) as dif
		';
		$sql .= '
			from
				sales_payments
			left join
				sales on sales.id = sales_payments.sale_id
			left join
				customers on customers.id = sales.customer_id
			left join
				peoples on peoples.id = customers.people_id
			where sales_payments.sale_id NOT IN (SELECT id FROM sales WHERE customer_id = 0)
			group by sales_payments.sale_id
		';

		DB::statement($sql);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement( 'DROP VIEW credit_sales' );
	}

}
