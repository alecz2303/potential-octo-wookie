<?php

class SalesController extends PosDashboardController {

	/**
	* Sales Model
	* @var Sales
	* @var SalesItems
	* @var SalesPayments
	* @var SalesItemsTaxes
	* @var AppConfig
	* @var Items
	* @var ItemsTaxes
	* @var Inventories
	* @var ItemQuantities
	*/
	protected $sales;
	protected $sales_items;
	protected $sales_items_taxes;
	protected $sales_payments;
	protected $app_config;
	protected $items;
	protected $items_taxes;
	protected $inventories;
	protected $item_quantities;

	/**
	* Inject the models.
	* @param Sales $sales
	*/
	public function __construct(AppConfig $app_config, Sales $sales, SalesItems $sales_items, SalesItemsTaxes $sales_items_taxes, SalesPayments $sales_payments, Items $items, ItemsTaxes $items_taxes, Inventories $inventories, ItemQuantities $item_quantities)
	{
		parent::__construct();
		$this->app_config = $app_config;
		$this->sales = $sales;
		$this->sales_items = $sales_items;
		$this->sales_items_taxes = $sales_items_taxes;
		$this->sales_payments = $sales_payments;
		$this->items = $items;
		$this->items_taxes = $items_taxes;
		$this->inventories = $inventories;
		$this->item_quantities = $item_quantities;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = "Venta de Articulos";
		return View::make('pos/sales/index', compact('title'));
	}

	public function postIndex()
	{
		echo "<pre>";
		print_r(Input::all());
		echo "</pre>";
	}

	public function getAuto()
	{
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('autocomplete_sales')
		->distinct()
		->where('name', 'LIKE', '%'.$term.'%')
		->orWhere('item_number', 'LIKE', '%'.$term.'%')
		->orWhere('description', 'LIKE', '%'.$term.'%')
		->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'tipo' => $query->Tipo, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description ];
		}
		return Response::json($results);
	}

	public function getAutocompletekit(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('item_kit_items')
		->leftjoin('items','item_kit_items.item_id','=','items.id')
		->leftjoin('item_quantities','item_quantities.item_id','=','item_kit_items.item_id')
		->where('item_kit_items.items_kits_id','=',$term)
		->select(array('item_kit_items.quantity as kitqty','items.id','items.name','items.item_number','items.description','items.unit_price','item_quantities.quantity'))
		->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->unit_price, 'qty' => $query->quantity, 'kitqty' => $query->kitqty ];
		}
		return Response::json($results);
	}

	public function getAutocompleteitem(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('items')
		->distinct()
		->leftjoin('item_quantities','item_quantities.item_id','=','items.id')
		->select(array('items.id','items.name','items.item_number','items.description','items.cost_price','item_quantities.quantity'))
		->where('items.id', '=', $term)
		->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->cost_price, 'qty' => $query->quantity ];
		}
		return Response::json($results);
	}

	public function getCustomers(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('peoples')
		->rightjoin('customers','peoples.id','=','customers.people_id')
		->Where('customers.deleted','=',0)
		->Where(function($query)
		{
			$term = Input::get('term');
			$query->Where('peoples.first_name', 'LIKE', '%'.$term.'%')
			->orWhere('peoples.last_name', 'LIKE', '%'.$term.'%');
		})
		->select(array('customers.id','peoples.first_name','peoples.last_name'))
		->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'customer_name' => $query->first_name.' '.$query->last_name ];
		}
		return Response::json($results);
	}

}
