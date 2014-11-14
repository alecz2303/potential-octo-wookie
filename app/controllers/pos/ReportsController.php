<?php

class ReportsController extends PosDashboardController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Reportes';
		return View::make('pos/reports/index',compact('title'));
	}

	public function getAsklowinventory()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/low_inventory/ask_low_inventory',compact('title'));
	}

	public function postAsklowinventory()
	{
		if(Input::get('saveExcel')){
			return Redirect::to('pos/reports/low_inventory/lowinventorypdf');
		}else{
			return Redirect::to('pos/reports/low_inventory/low');
		}
	}

	public function getLow()
	{
		$title = "Reporte de Inventario Bajo";
		return View::make('pos/reports/low_inventory/low_inventory',compact('title'));
	}

	public function getDatalow(){
		$items = Items::leftjoin('item_quantities', 'items.id', '=', 'item_quantities.item_id')
					->select(array('items.id', 'items.name', 'items.item_number', 'items.description','item_quantities.quantity', 'reorder_level'))
					->where('items.deleted','=','0')
					->whereRaw('item_quantities.quantity <= items.reorder_level');


		return Datatables::of($items)

		->remove_column('id')

		->make();
	}

	public function getLowinventorypdf()
	{
		$pdf = PDF::loadView('pos/reports/low_inventory/low_inventory_pdf');
		return $pdf->download('inventario.pdf');
	}

	public function getAskinventory()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/inventory/ask_inventory',compact('title'));
	}

	public function postAskinventory()
	{
		if(Input::get('savePDF')){
			return Redirect::to('pos/reports/inventory/inventorypdf');
		}else{
			return Redirect::to('pos/reports/inventory/inventory');
		}
	}

	public function getInventory()
	{
		$title = "Reporte de Inventario";
		return View::make('pos/reports/inventory/inventory',compact('title'));
	}

	public function getDatainventory(){
		$items = Items::leftjoin('item_quantities', 'items.id', '=', 'item_quantities.item_id')
					->select(array('items.id', 'items.name', 'items.item_number', 'items.description','item_quantities.quantity', 'reorder_level'))
					->where('items.deleted','=','0');

		return Datatables::of($items)

		->remove_column('id')

		->make();
	}

	public function getInventorypdf()
	{
		$pdf = PDF::loadView('pos/reports/inventory/inventory_pdf');
		return $pdf->download('inventario.pdf');
	}

	public function getSummarysales()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_sales/index',compact('title'));
	}

	public function postSummarysales()
	{
		echo "<pre>";
		print_r(Input::all());
		echo "</pre>";

		if(Input::get('option')){
			$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
								->selectRaw('sales_items.sale_id,sales_items.created_at,
											sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
											(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
											sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
											(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
											sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
											sum((quantity_purchased * item_cost_price)) as ganancia')
								->whereRaw('sales_items.created_at between '.Input::get('date_range'))
								->groupBy('sales_items.sale_id')
								->get();
			echo "<pre>";
			print_r($sales);
			echo "</pre>";
		}
	}

}
