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

		if(Input::get('option')==1){
			$date_range = Input::get('date_range');
		}else{
			#################################
			##		Mensajes de Error      ##
			#################################
			$messages = array(
				'start_date.required' => 'Debe seleccionar una fecha de inicio',
				'end_date.required' => 'Debe seleccionar una fecha final',
				'end_date.after' => 'Debe mayor a la fecha de inicio',
			);

			#################################
			##		Datos a validar        ##
			#################################
			$data = array(
				'start_date' => Input::get('start_date'),
				'end_date' => Input::get('end_date')
			);

			#################################
			##		Reglas de validación   ##
			#################################
			$rules = array(
				'start_date' => 'required',
				'end_date' => 'required|after:start_date'
			);

			#################################
			##    Validación de los datos  ##
			#################################
			$validator = Validator::make($data,$rules,$messages);

			if($validator->fails()){
				$messages = $validator->messages();
				echo "<hr>";
				echo "<pre>";
				print_r($messages);
				echo "</pre>";
				return Redirect::to('pos/reports/summary_sales')
								->withErrors($messages)
								->withInput();
			}
			$date_range = "'".Input::get('start_date')."' and ".date("'".Input::get('end_date')." 23:59:59'");
		}
		if(Input::get('sale_type')==0){
			$whereRaw = "quantity_purchased <> 'a'";
		}elseif(Input::get('sale_type')==1){
			$whereRaw = "quantity_purchased >= '0'";
		}elseif(Input::get('sale_type')==2){
			$whereRaw = "quantity_purchased < '0'";
		}
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->selectRaw('sales_items.sale_id,sales_items.created_at,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('sales_items.sale_id')
							->orderBy('sales_items.created_at')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_sales/summary_sales_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_sales.pdf');
		}else{
			return View::make('pos/reports/summary_sales/report', compact('sales','date_range','whereRaw'));
		}
	}

		public function getDatasummarysales(){
			$date_range = Input::get('date_range');
			$whereRaw = Input::get('whereRaw');
			$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
								->selectRaw('sales_items.created_at,
								FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
								FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
								FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
								(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
								FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
								sum((quantity_purchased * item_cost_price)),2) as ganancia')
								->whereRaw('sales_items.created_at between '.$date_range)
								->whereRaw($whereRaw)
								->groupBy('sales_items.sale_id')
								->orderBy('sales_items.created_at');
								return Datatables::of($sales)
								->make();
		}

		public function getSummarycategories()
		{
			$title = "Entrada de Reporte";
			return View::make('pos/reports/summary_categories/index',compact('title'));
		}

		public function postSummarycategories()
		{

			if(Input::get('option')==1){
				$date_range = Input::get('date_range');
			}else{
				#################################
				##		Mensajes de Error      ##
				#################################
				$messages = array(
					'start_date.required' => 'Debe seleccionar una fecha de inicio',
					'end_date.required' => 'Debe seleccionar una fecha final',
					'end_date.after' => 'Debe mayor a la fecha de inicio',
				);

				#################################
				##		Datos a validar        ##
				#################################
				$data = array(
					'start_date' => Input::get('start_date'),
					'end_date' => Input::get('end_date')
				);

				#################################
				##		Reglas de validación   ##
				#################################
				$rules = array(
					'start_date' => 'required',
					'end_date' => 'required|after:start_date'
				);

				#################################
				##    Validación de los datos  ##
				#################################
				$validator = Validator::make($data,$rules,$messages);

				if($validator->fails()){
					$messages = $validator->messages();
					echo "<hr>";
					echo "<pre>";
					print_r($messages);
					echo "</pre>";
					return Redirect::to('pos/reports/summary_categories')
									->withErrors($messages)
									->withInput();
				}
				$date_range = "'".Input::get('start_date')."' and ".date("'".Input::get('end_date')." 23:59:59'");
			}
			if(Input::get('sale_type')==0){
				$whereRaw = "quantity_purchased <> 'a'";
			}elseif(Input::get('sale_type')==1){
				$whereRaw = "quantity_purchased >= '0'";
			}elseif(Input::get('sale_type')==2){
				$whereRaw = "quantity_purchased < '0'";
			}
			$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
								->leftjoin('items','items.id','=','sales_items.item_id')
								->selectRaw('items.category,
											sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
											(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
											sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
											(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
											sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
											sum((quantity_purchased * item_cost_price)) as ganancia')
								->whereRaw('sales_items.created_at between '.$date_range)
								->whereRaw($whereRaw)
								->groupBy('items.category')
								->orderBy('sales_items.created_at')
								->get();
			if(Input::get('savePDF')){
				$pdf = PDF::loadView('pos/reports/summary_categories/summary_categories_pdf',compact('sales','date_range'));
				return $pdf->stream('summary_categories.pdf');
			}else{
				return View::make('pos/reports/summary_categories/report', compact('sales','date_range','whereRaw'));
			}
		}

			public function getDatasummarycategories(){
				$date_range = Input::get('date_range');
				$whereRaw = Input::get('whereRaw');
				$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
									->leftjoin('items','items.id','=','sales_items.item_id')
									->selectRaw('items.category,
									FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
									FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
									FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
									(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
									FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
									sum((quantity_purchased * item_cost_price)),2) as ganancia')
									->whereRaw('sales_items.created_at between '.$date_range)
									->whereRaw($whereRaw)
									->groupBy('items.category')
									->orderBy('sales_items.created_at');
									return Datatables::of($sales)
									->make();
			}

}
