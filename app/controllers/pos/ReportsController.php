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
		return $pdf->stream('inventario.pdf');
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
		return $pdf->stream('inventario.pdf');
	}

	public function getSummarysales()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_sales/index',compact('title'));
	}

###############################################################
##                  										 ##
##                    SUMMARY SALES                          ##
##                                                           ##
###############################################################

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

###############################################################
##                  										 ##
##                    SUMMARY SALES                          ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    SUMMARY CATEGORIES                     ##
##                                                           ##
###############################################################

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

###############################################################
##                  										 ##
##                    SUMMARY CATEGORIES                     ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    SUMMARY CUSTOMERS                      ##
##                                                           ##
###############################################################

	public function getSummarycustomers()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_customers/index',compact('title'));
	}

	public function postSummarycustomers()
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
				return Redirect::to('pos/reports/summary_customers')
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
							->leftjoin('sales','sales.id','=','lpos.sales_items.sale_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as full_name,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('peoples.first_name','peoples.last_name')
							->orderBy('peoples.last_name','peoples.first_name')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_customers/summary_customers_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_customers.pdf');
		}else{
			return View::make('pos/reports/summary_customers/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummarycustomers(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->leftjoin('sales','sales.id','=','lpos.sales_items.sale_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as full_name,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
										FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)),2) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('peoples.first_name','peoples.last_name');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY CUSTOMERS                      ##
##                                                           ##
###############################################################


###############################################################
##                  										 ##
##                    SUMMARY SUPPLIERS                      ##
##                                                           ##
###############################################################

	public function getSummarysuppliers()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_suppliers/index',compact('title'));
	}

	public function postSummarysuppliers()
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
				return Redirect::to('pos/reports/summary_suppliers')
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
							->leftjoin('suppliers','suppliers.id','=','items.supplier_id')
							->selectRaw('company_name,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('company_name')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_suppliers/summary_suppliers_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_suppliers.pdf');
		}else{
			return View::make('pos/reports/summary_suppliers/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummarysuppliers(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->leftjoin('items','items.id','=','sales_items.item_id')
							->leftjoin('suppliers','suppliers.id','=','items.supplier_id')
							->selectRaw('company_name,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
										FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)),2) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('company_name');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY SUPPLIERS                      ##
##                                                           ##
###############################################################


###############################################################
##                  										 ##
##                    SUMMARY ITEMS                          ##
##                                                           ##
###############################################################

	public function getSummaryitems()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_items/index',compact('title'));
	}

	public function postSummaryitems()
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
				return Redirect::to('pos/reports/summary_items')
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
							->selectRaw('items.name,sum(quantity_purchased) as quantity_purchased,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('items.name')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_items/summary_items_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_items.pdf');
		}else{
			return View::make('pos/reports/summary_items/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummaryitems(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->leftjoin('items','items.id','=','sales_items.item_id')
							->selectRaw('items.name,sum(quantity_purchased) as quantity_purchased,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
										FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)),2) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('items.name');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY ITEMS                          ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    SUMMARY USERS                          ##
##                                                           ##
###############################################################

	public function getSummaryusers()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_users/index',compact('title'));
	}

	public function postSummaryusers()
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
				return Redirect::to('pos/reports/summary_users')
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
							->rightjoin('sales','sales.id','=','sales_items.sale_id')
							->leftjoin('users','users.id','=','sales.user_id')
							->selectRaw('username,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('username')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_users/summary_users_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_users.pdf');
		}else{
			return View::make('pos/reports/summary_users/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummaryusers(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->rightjoin('sales','sales.id','=','sales_items.sale_id')
							->leftjoin('users','users.id','=','sales.user_id')
							->selectRaw('username,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
										FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)),2) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('username');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY USERS                          ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    SUMMARY TAXES                          ##
##                                                           ##
###############################################################

	public function getSummarytaxes()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_taxes/index',compact('title'));
	}

	public function postSummarytaxes()
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
				return Redirect::to('pos/reports/summary_taxes')
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
							->selectRaw('sales_items_taxes.name,sales_items_taxes.percent,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('sales_items_taxes.name','sales_items_taxes.percent')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_taxes/summary_taxes_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_taxes.pdf');
		}else{
			return View::make('pos/reports/summary_taxes/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummarytaxes(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->selectRaw('sales_items_taxes.name,sales_items_taxes.percent,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
										FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)),2) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('sales_items_taxes.name','sales_items_taxes.percent');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY TAXES                          ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    SUMMARY DISCOUNTS                      ##
##                                                           ##
###############################################################

	public function getSummarydiscounts()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_discounts/index',compact('title'));
	}

	public function postSummarydiscounts()
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
				return Redirect::to('pos/reports/summary_discounts')
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
							->selectRaw('sales_items.discount_percent,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('sales_items.discount_percent')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_discounts/summary_discounts_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_discounts.pdf');
		}else{
			return View::make('pos/reports/summary_discounts/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummarydiscounts(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->selectRaw('sales_items.discount_percent,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
										FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
										FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)),2) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('sales_items.discount_percent');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY DISCOUNTS                      ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    SUMMARY PAYMENTS                       ##
##                                                           ##
###############################################################

	public function getSummarypayments()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/summary_payments/index',compact('title'));
	}

	public function postSummarypayments()
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
				return Redirect::to('pos/reports/summary_payments')
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
							->leftjoin('sales_payments','sales_payments.sale_id','=','sales_items_taxes.sale_id')
							->selectRaw('payment_type,sum(if(quantity_purchased < 0,(payment_amount*-1),payment_amount)) as payment_amount,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('payment_type')
							->get();
		if(Input::get('savePDF')){
			$pdf = PDF::loadView('pos/reports/summary_payments/summary_payments_pdf',compact('sales','date_range'));
			return $pdf->stream('summary_payments.pdf');
		}else{
			return View::make('pos/reports/summary_payments/report', compact('sales','date_range','whereRaw'));
		}
	}

	public function getDatasummarypayments(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesPayments::leftjoin('sales_items','sales_items.sale_id','=','sales_payments.sale_id')
							->selectRaw('payment_type,FORMAT(sum(if(quantity_purchased < 0,(payment_amount*-1),payment_amount)),2) as payment_amount')
							->whereRaw('sales_payments.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->where('line','=',1)
							->groupBy('payment_type');
							return Datatables::of($sales)
							->make();
	}

###############################################################
##                  										 ##
##                    SUMMARY PAYMENTS                       ##
##                                                           ##
###############################################################

	public function getGraphicsales(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_sales/index',compact('title'));
	}

	public function postGraphicsales(){
		$title = 'Entrada de Reporte';
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
				return Redirect::to('pos/reports/graphic/sales')
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

		/*$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->selectRaw('SUBSTRING(sales_items.created_at,1,10) as created_at,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupByRaw('SUBSTRING(sales_items.created_at,1,10)')
							->orderBy('SUBSTRING(sales_items.created_at,1,10)')
							->get();*/

		$sales = DB::select( DB::raw("SELECT SUBSTRING(sales_items.created_at,1,10) as created_at,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as 'subtotal',
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										SUBSTRING(sales_items.created_at,1,10)"
									));


		return View::make('pos/reports/graphic_sales/sales',compact('title','sales','date_range'));
	}

	public function getGraphiccategory(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_category/index',compact('title'));
	}

	public function postGraphiccategory(){
		$title = 'Entrada de Reporte';
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
				return Redirect::to('pos/reports/graphic/category')
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

		/*$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->selectRaw('SUBSTRING(sales_items.created_at,1,10) as created_at,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupByRaw('SUBSTRING(sales_items.created_at,1,10)')
							->orderBy('SUBSTRING(sales_items.created_at,1,10)')
							->get();*/

		$category = DB::select( DB::raw("SELECT SUBSTRING(sales_items.created_at,1,10) as created_at,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as 'subtotal',
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										SUBSTRING(sales_items.created_at,1,10)"
									));


		return View::make('pos/reports/graphic_category/category',compact('title','category','date_range'));
	}

}
