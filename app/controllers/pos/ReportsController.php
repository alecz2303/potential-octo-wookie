<?php

class ReportsController extends PosDashboardController {

	/**
	* Customers Model
	* @var giftcards
	*/
	protected $sales;

	/**
	* Inject the models.
	* @param Customers $suppliers
	*/
	public function __construct(Sales $sales)
	{
		parent::__construct();
		$this->sales = $sales;
	}

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

###############################################################
##                  										 ##
##                    SUMMARY SALES                          ##
##                                                           ##
###############################################################

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

###############################################################
##                  										 ##
##                END SUMMARY SALES                          ##
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
##                END SUMMARY CATEGORIES                     ##
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
							->leftjoin('sales','sales.id','=','sales_items.sale_id')
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
							->leftjoin('sales','sales.id','=','sales_items.sale_id')
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
##               END  SUMMARY CUSTOMERS                      ##
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
##               END  SUMMARY SUPPLIERS                      ##
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
##               END  SUMMARY ITEMS                          ##
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
##                END SUMMARY USERS                          ##
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
##                END SUMMARY TAXES                          ##
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
##                END SUMMARY DISCOUNTS                      ##
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
							->selectRaw('payment_type,sum(if(quantity_purchased < 0,(payment_amount*-1),payment_amount)) as payment_amount')
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
##                END SUMMARY PAYMENTS                       ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    GRAPHIC SALES                          ##
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

###############################################################
##                  										 ##
##                    END GRAPHIC SALES                      ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    GRAPHIC CATEGORY                       ##
##                                                           ##
###############################################################
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

		$category = DB::select( DB::raw("SELECT items.category,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									LEFT JOIN
										items ON items.id = sales_items.item_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										items.category"
									));


		return View::make('pos/reports/graphic_category/category',compact('title','category','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC CATEGORY                   ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    GRAPHIC CUSTOMERS                      ##
##                                                           ##
###############################################################

	public function getGraphiccustomer(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_customer/index',compact('title'));
	}

	public function postGraphiccustomer(){
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
				return Redirect::to('pos/reports/graphic/customer')
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

		$customer = DB::select( DB::raw("SELECT ifnull(CONCAT(peoples.first_name,' ',peoples.last_name),'Mostrador') as full_name,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									LEFT JOIN
										sales on sales.id = sales_items.sale_id
									LEFT JOIN
										customers ON customers.id = sales.customer_id
									LEFT JOIN
										peoples ON peoples.id = customers.people_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										ifnull(CONCAT(peoples.first_name,' ',peoples.last_name),'Mostrador')"
									));


		return View::make('pos/reports/graphic_customer/customer',compact('title','customer','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC CUSTOMER                   ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                   GRAPHIC SUPPLIER                        ##
##                                                           ##
###############################################################

	public function getGraphicsupplier(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_supplier/index',compact('title'));
	}

	public function postGraphicsupplier(){
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
				return Redirect::to('pos/reports/graphic/supplier')
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

		$supplier = DB::select( DB::raw("SELECT company_name,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									LEFT JOIN
										items ON items.id = sales_items.item_id
									LEFT JOIN
										suppliers ON suppliers.id = items.supplier_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										company_name"
									));


		return View::make('pos/reports/graphic_supplier/supplier',compact('title','supplier','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC SUPPLIER                   ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    GRAPHIC ITEM                           ##
##                                                           ##
###############################################################

	public function getGraphicitem(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_item/index',compact('title'));
	}

	public function postGraphicitem(){
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
				return Redirect::to('pos/reports/graphic/item')
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

		$item = DB::select( DB::raw("SELECT items.name,sum(quantity_purchased) as quantity_purchased,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									LEFT JOIN
										items ON items.id = sales_items.item_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										items.name"
									));


		return View::make('pos/reports/graphic_item/item',compact('title','item','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC ITEM                       ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                   GRAPHIC USER                            ##
##                                                           ##
###############################################################

	public function getGraphicuser(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_user/index',compact('title'));
	}

	public function postGraphicuser(){
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
				return Redirect::to('pos/reports/graphic/user')
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

		$user = DB::select( DB::raw("SELECT username,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
										(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
										sum((quantity_purchased * item_cost_price)) as ganancia
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									RIGHT JOIN
										sales ON sales.id = sales_items.sale_id
									LEFT JOIN
										users ON users.id = sales.user_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										username"
									));


		return View::make('pos/reports/graphic_user/user',compact('title','user','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC USER                       ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                   GRAPHIC TAX                             ##
##                                                           ##
###############################################################

	public function getGraphictax(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_tax/index',compact('title'));
	}

	public function postGraphictax(){
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
				return Redirect::to('pos/reports/graphic/tax')
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

		$tax_ = DB::select( DB::raw("SELECT sales_items_taxes.name,sales_items_taxes.percent,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
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
										sales_items_taxes.name,sales_items_taxes.percent"
									));


		return View::make('pos/reports/graphic_tax/tax',compact('title','tax_','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC TAX                        ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    GRAPHIC DISCOUNT                      ##
##                                                           ##
###############################################################

	public function getGraphicdiscount(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_discount/index',compact('title'));
	}

	public function postGraphicdiscount(){
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
				return Redirect::to('pos/reports/graphic/discount')
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

		$discount = DB::select( DB::raw("SELECT sales_items.discount_percent,count(sales_items.discount_percent) as disc_count,
										sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as subtotal,
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
										$whereRaw and
										discount_percent > 0
									GROUP BY
										sales_items.discount_percent"
									));


		return View::make('pos/reports/graphic_discount/discount',compact('title','discount','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC DISCOUNT                   ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                   GRAPHIC PAYMENT                         ##
##                                                           ##
###############################################################

	public function getGraphicpayment(){
		$title = 'Entrada de Reporte';
		return View::make('pos/reports/graphic_payment/index',compact('title'));
	}

	public function postGraphicpayment(){
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
				return Redirect::to('pos/reports/graphic/payment')
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

		$payment = DB::select( DB::raw("SELECT payment_type,sum(if(quantity_purchased < 0,(payment_amount*-1),payment_amount)) as payment_amount
									FROM
										sales_items
									LEFT JOIN
										sales_items_taxes on sales_items.sale_id = sales_items_taxes.sale_id
									LEFT JOIN
										sales_payments ON sales_payments.sale_id = sales_items_taxes.sale_id
									WHERE
										sales_items.created_at between $date_range and
										$whereRaw
									GROUP BY
										payment_type"
									));


		return View::make('pos/reports/graphic_payment/payment',compact('title','payment','date_range'));
	}

###############################################################
##                  										 ##
##                    END GRAPHIC PAYMENT                    ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    DETAIL SALES                           ##
##                                                           ##
###############################################################

	public function getDetailsales()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/detail_sales/index',compact('title'));
	}

	public function postDetailsales()
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
				return Redirect::to('pos/reports/detail_sales')
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
												->leftjoin('sales','sales.id','=','sales_items.sale_id')
												->leftjoin('users','users.id','=','sales.user_id')
												->leftjoin('customers','customers.id','=','sales.customer_id')
												->leftjoin('peoples','peoples.id','=','customers.people_id')
												->selectRaw('sales_items.sale_id,SUM(sales_items.quantity_purchased) as items_purchased,username,
												ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as customer,
												SUBSTRING(sales_items.created_at,1,10) as created_at,
												FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
												FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
												FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
												(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
												FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
												sum((quantity_purchased * item_cost_price)),2) as ganancia,payment_type')
												->whereRaw('sales_items.created_at between '.$date_range)
												->whereRaw($whereRaw)
												->groupBy('sales_items.sale_id')
												->orderBy('sales_items.created_at')
							->get();
		return View::make('pos/reports/detail_sales/report', compact('sales','date_range','whereRaw'));
	}

	public function getDatadetailsales(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$sales = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->leftjoin('sales','sales.id','=','sales_items.sale_id')
							->leftjoin('users','users.id','=','sales.user_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('sales_items.sale_id,SUBSTRING(sales_items.created_at,1,10) as created_at,
							SUM(sales_items.quantity_purchased) as items_purchased,username,
							ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as customer,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
							FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
							(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
							sum((quantity_purchased * item_cost_price)),2) as ganancia,sales.payment_type,sales.comment')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw($whereRaw)
							->groupBy('sales_items.sale_id')
							->orderBy('sales_items.created_at');
							return Datatables::of($sales)
							->edit_column('sale_id', '<a href="{{URL::to("pos/sales/$sale_id/receipt")}}" target="_blank" data-tooltip aria-haspopup="true" class="has-tip" title="POS # {{$sale_id}}">{{$sale_id}} </a>')
							->add_column('actions', '<ul class="stack button-group round">
														<li><a href="{{{ URL::to(\'pos/reports/detail_sales/\' . $sale_id . \'/edit\' ) }}}" class="iframe2 button tiny">{{{ Lang::get(\'button.edit\') }}}</a></li>
													</ul>
							')
							->make();
	}

	public function getEditsale($sales)
	{
		$title = 'Edición de Ventas';
		$mode = 'edit';

		$customer_options = DB::table('customers')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('customers.id,CONCAT(peoples.first_name," ",peoples.last_name) as full_name')
							->where('deleted','=',0)
							->orderBy('peoples.last_name', 'asc')
							->lists('full_name','id');
		$user_options = DB::table('users')
						->where('confirmed','=',1)
						->orderBy('username')
						->lists('username','id');

		return View::make('pos/reports/detail_sales/edit_sale', compact(array('sales','title','mode','customer_options','user_options')));
	}

	public function postEditsale($sales)
	{
		$sales->customer_id = Input::get('customer_id');
		$sales->user_id = Input::get('user_id');
		$sales->comment = Input::get('comment');
		if($sales->save()){
			return Redirect::to('pos/reports/detail_sales/'.$sales->id.'/edit')->with('success', 'Se han guardado los cambios con éxito');
		}else{
			return Redirect::to('pos/reports/detail_sales/'.$sales->id.'/edit')->with('error', 'No se han guardado los cambios, por favor intente mas tarde.');
		}
	}
###############################################################
##                  										 ##
##                END DETAIL SALES                           ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                DETAIL RECEIVINGS                          ##
##                                                           ##
###############################################################


	public function getDetailreceivings()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/detail_receivings/index',compact('title'));
	}

	public function postDetailreceivings()
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
				return Redirect::to('pos/reports/detail_sales')
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
		$receivings = Receivings::leftjoin('receivings_items','receivings_items.receivings_id','=','receivings.id')
								->leftjoin('users','users.id','=','receivings.user_id')
								->leftjoin('suppliers','suppliers.id','=','receivings.supplier_id')
								->selectRaw('receivings.id,SUBSTRING(receivings.created_at,1,10) as created_at,SUM(quantity_purchased) as items_purchased,
											username,company_name,SUM(quantity_purchased*item_cost_price) as total,payment_type,comment')
								->whereRaw('receivings.created_at between '.$date_range)
								->whereRaw($whereRaw)
								->groupBy('receivings.id')
								->orderBy('receivings.created_at')
								->get();
		return View::make('pos/reports/detail_receivings/report', compact('receivings','date_range','whereRaw'));
	}

	public function getDatadetailreceivings(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$receivings = Receivings::leftjoin('receivings_items','receivings_items.receivings_id','=','receivings.id')
					->leftjoin('users','users.id','=','receivings.user_id')
					->leftjoin('suppliers','suppliers.id','=','receivings.supplier_id')
					->selectRaw('receivings.id,SUBSTRING(receivings.created_at,1,10) as created_at,SUM(quantity_purchased) as items_purchased,
								username,company_name,FORMAT(SUM(quantity_purchased*item_cost_price),2) as "total",receivings.payment_type,receivings.comment')
					->whereRaw('receivings.created_at between '.$date_range)
					->whereRaw($whereRaw)
					->groupBy('receivings.id')
					->orderBy('receivings.created_at');

					return Datatables::of($receivings)
					->edit_column('id', '<a href="{{URL::to("pos/receivings/$id/receipt")}}" target="_blank" data-tooltip aria-haspopup="true" class="has-tip" title="REC # {{$id}}">{{$id}} </a>')
					->make();
	}
###############################################################
##                  										 ##
##               END DETAIL RECEIVINGS                       ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    DETAIL CUSTOMER                        ##
##                                                           ##
###############################################################

	public function getDetailcustomers()
	{
		$title = "Entrada de Reporte";
		$customer_options = DB::table('customers')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('customers.id,CONCAT(peoples.first_name," ",peoples.last_name) as full_name')
							->where('deleted','=',0)
							->orderBy('peoples.last_name', 'asc')
							->lists('full_name','id');
		return View::make('pos/reports/detail_customers/index',compact('title','customer_options'));
	}

	public function postDetailcustomers()
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
				return Redirect::to('pos/reports/detail_customers')
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
		$customer_id = Input::get('customer_id');
		$customer = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
												->leftjoin('sales','sales.id','=','sales_items.sale_id')
												->leftjoin('users','users.id','=','sales.user_id')
												->leftjoin('customers','customers.id','=','sales.customer_id')
												->leftjoin('peoples','peoples.id','=','customers.people_id')
												->selectRaw('sales_items.sale_id,SUM(sales_items.quantity_purchased) as items_purchased,username,
												ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as customer,
												SUBSTRING(sales_items.created_at,1,10) as created_at,
												sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
												(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
												sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
												(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
												sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
												sum((quantity_purchased * item_cost_price)) as ganancia,payment_type')
												->whereRaw('sales_items.created_at between '.$date_range)
												->whereRaw('sales.customer_id = "'.$customer_id.'"')
												->whereRaw($whereRaw)
												->groupBy('sales_items.sale_id')
												->orderBy('sales_items.created_at')
							->get();
		return View::make('pos/reports/detail_customers/report', compact('customer','date_range','whereRaw','customer_id'));
	}

	public function getDatadetailcustomers(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$customer_id = Input::get('customer_id');
		$customer = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->leftjoin('sales','sales.id','=','sales_items.sale_id')
							->leftjoin('users','users.id','=','sales.user_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('sales_items.sale_id,SUBSTRING(sales_items.created_at,1,10) as created_at,
							SUM(sales_items.quantity_purchased) as items_purchased,username,
							ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as customer,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
							FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
							(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
							sum((quantity_purchased * item_cost_price)),2) as ganancia,sales.payment_type,sales.comment')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw('sales.customer_id = "'.$customer_id.'"')
							->whereRaw($whereRaw)
							->groupBy('sales_items.sale_id')
							->orderBy('sales_items.created_at');
							return Datatables::of($customer)
							->edit_column('sale_id', '<a href="{{URL::to("pos/sales/$sale_id/receipt")}}" target="_blank" data-tooltip aria-haspopup="true" class="has-tip" title="POS # {{$sale_id}}">{{$sale_id}} </a>')
							->add_column('actions', '<ul class="stack button-group round">
														<li><a href="{{{ URL::to(\'pos/reports/detail_sales/\' . $sale_id . \'/edit\' ) }}}" class="iframe2 button tiny">{{{ Lang::get(\'button.edit\') }}}</a></li>
													</ul>
							')
							->make();
	}

	public function getEditcustomer($sales)
	{
		$title = 'Edición de Ventas';
		$mode = 'edit';

		$customer_options = DB::table('customers')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('customers.id,CONCAT(peoples.first_name," ",peoples.last_name) as full_name')
							->where('deleted','=',0)
							->orderBy('peoples.last_name', 'asc')
							->lists('full_name','id');
		$user_options = DB::table('users')
						->where('confirmed','=',1)
						->orderBy('username')
						->lists('username','id');

		return View::make('pos/reports/detail_sales/edit_sale', compact(array('sales','title','mode','customer_options','user_options')));
	}

	public function postEditcustomer($sales)
	{
		$sales->customer_id = Input::get('customer_id');
		$sales->user_id = Input::get('user_id');
		$sales->comment = Input::get('comment');
		if($sales->save()){
			return Redirect::to('pos/reports/detail_sales/'.$sales->id.'/edit')->with('success', 'Se han guardado los cambios con éxito');
		}else{
			return Redirect::to('pos/reports/detail_sales/'.$sales->id.'/edit')->with('error', 'No se han guardado los cambios, por favor intente mas tarde.');
		}
	}
###############################################################
##                  										 ##
##                END DETAIL CUSTOMER                        ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    DETAIL USER                            ##
##                                                           ##
###############################################################

	public function getDetailusers()
	{
		$title = "Entrada de Reporte";
		$user_options = DB::table('users')
							->where('confirmed','=',1)
							->orderBy('username')
							->lists('username','id');
		return View::make('pos/reports/detail_users/index',compact('title','user_options'));
	}

	public function postDetailusers()
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
				return Redirect::to('pos/reports/detail_users')
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
		$user_id = Input::get('user_id');
		$user = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
												->leftjoin('sales','sales.id','=','sales_items.sale_id')
												->leftjoin('users','users.id','=','sales.user_id')
												->leftjoin('customers','customers.id','=','sales.customer_id')
												->leftjoin('peoples','peoples.id','=','customers.people_id')
												->selectRaw('sales_items.sale_id,SUM(sales_items.quantity_purchased) as items_purchased,username,
												ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as customer,
												SUBSTRING(sales_items.created_at,1,10) as created_at,
												sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) as "subtotal",
												(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as tax,
												sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
												(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100) as total,
												sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
												sum((quantity_purchased * item_cost_price)) as ganancia,payment_type')
												->whereRaw('sales_items.created_at between '.$date_range)
												->whereRaw('sales.user_id = "'.$user_id.'"')
												->whereRaw($whereRaw)
												->groupBy('sales_items.sale_id')
												->orderBy('sales_items.created_at')
							->get();
		return View::make('pos/reports/detail_users/report', compact('user','date_range','whereRaw','user_id'));
	}

	public function getDatadetailusers(){
		$date_range = Input::get('date_range');
		$whereRaw = Input::get('whereRaw');
		$user_id = Input::get('user_id');
		$user = SalesItems::leftjoin('sales_items_taxes','sales_items.sale_id','=','sales_items_taxes.sale_id')
							->leftjoin('sales','sales.id','=','sales_items.sale_id')
							->leftjoin('users','users.id','=','sales.user_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('sales_items.sale_id,SUBSTRING(sales_items.created_at,1,10) as created_at,
							SUM(sales_items.quantity_purchased) as items_purchased,username,
							ifnull(CONCAT(peoples.first_name," ",peoples.last_name),"Mostrador") as customer,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)),2) as "subtotal",
							FORMAT((sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as tax,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100)) +
							(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))) * (percent/100),2) as total,
							FORMAT(sum((quantity_purchased * item_unit_price)) - sum((quantity_purchased * item_unit_price) * (discount_percent/100))-
							sum((quantity_purchased * item_cost_price)),2) as ganancia,sales.payment_type,sales.comment')
							->whereRaw('sales_items.created_at between '.$date_range)
							->whereRaw('sales.user_id = "'.$user_id.'"')
							->whereRaw($whereRaw)
							->groupBy('sales_items.sale_id')
							->orderBy('sales_items.created_at');
							return Datatables::of($user)
							->edit_column('sale_id', '<a href="{{URL::to("pos/sales/$sale_id/receipt")}}" target="_blank" data-tooltip aria-haspopup="true" class="has-tip" title="POS # {{$sale_id}}">{{$sale_id}} </a>')
							->add_column('actions', '<ul class="stack button-group round">
														<li><a href="{{{ URL::to(\'pos/reports/detail_sales/\' . $sale_id . \'/edit\' ) }}}" class="iframe2 button tiny">{{{ Lang::get(\'button.edit\') }}}</a></li>
													</ul>
							')
							->make();
	}

	public function getEdituser($sales)
	{
		$title = 'Edición de Ventas';
		$mode = 'edit';

		$customer_options = DB::table('customers')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('customers.id,CONCAT(peoples.first_name," ",peoples.last_name) as full_name')
							->where('deleted','=',0)
							->orderBy('peoples.last_name', 'asc')
							->lists('full_name','id');
		$user_options = DB::table('users')
						->where('confirmed','=',1)
						->orderBy('username')
						->lists('username','id');

		return View::make('pos/reports/detail_users/edit_sale', compact(array('sales','title','mode','customer_options','user_options')));
	}

	public function postEdituser($sales)
	{
		$sales->customer_id = Input::get('customer_id');
		$sales->user_id = Input::get('user_id');
		$sales->comment = Input::get('comment');
		if($sales->save()){
			return Redirect::to('pos/reports/detail_sales/'.$sales->id.'/edit')->with('success', 'Se han guardado los cambios con éxito');
		}else{
			return Redirect::to('pos/reports/detail_sales/'.$sales->id.'/edit')->with('error', 'No se han guardado los cambios, por favor intente mas tarde.');
		}
	}
###############################################################
##                  										 ##
##                END DETAIL USER                            ##
##                                                           ##
###############################################################

###############################################################
##                  										 ##
##                    CREDIT SALES                           ##
##                                                           ##
###############################################################

	public function getCreditsales()
	{
		$title = "Entrada de Reporte";
		return View::make('pos/reports/credit_sales/index',compact('title'));
	}

	public function postCreditsales()
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
				return Redirect::to('pos/reports/credit_sales')
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
		$sales = SalesPayments::leftjoin('sales','sales.id','=','sales_payments.sale_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('sales_payments.sale_id,SUBSTRING(sales.created_at,1,10) as created_at,CONCAT(first_name," ",last_name) as full_name,SUM(payment_amount) as payment_amount,
							(SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) as subtotal,
							(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id) as tax,
							(SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) +
							(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id) as total,
							((SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) +
							(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id)) - SUM(payment_amount) as dif')
							->whereRaw('sales_payments.sale_id NOT IN (SELECT id FROM sales WHERE customer_id = 0)')
							->whereRaw('sales.created_at between '.$date_range)
							//->whereRaw($whereRaw)
							->groupBy('sales_payments.sale_id')
							->orderBy('sales.created_at')
							->get();
		return View::make('pos/reports/credit_sales/report', compact('sales','date_range','whereRaw'));
	}

	public function getAddpayment($sales,$dif)
	{
		$title = 'Agregar pago';
		return View::make('pos/reports/credit_sales/add_payment', compact('sales','dif','title'));
	}
###############################################################
##                  										 ##
##                END CREDIT SALES                           ##
##                                                           ##
###############################################################

}
