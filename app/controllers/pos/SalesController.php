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

		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'entry.required' => 'Debe tener al menos un articulo',
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
			'customer_id' => Input::get('customer_id'),
			'user_id' => Auth::user()->id,
			'comment' => Input::get('comment'),
			'payment_type' => Input::get('payment_type'),
			'entry' => Input::get('entry'),
			'pay_qty' => Input::get('pay_qty'),
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
			'comment' => 'min:3',
			'entry' => 'required|array',
			'pay_qty' => 'numeric',
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
			return Redirect::to('pos/sales')
							->withErrors($messages)
							->withInput();
		}
		$this->sales->customer_id = Input::get('customer_id');
		$this->sales->user_id = Auth::user()->id;
		$this->sales->comment = Input::get('comment');
		$this->sales->payment_type = '';
		foreach (Input::get('payment') as $type => $value) {
			$this->sales->payment_type .= $type.': '.$value.'<br/>';
			$tipoPago = str_split($type,10);
			//echo $type;
			foreach ($tipoPago as $key => $val) {
				if($val == 'Gift Card:'){
					echo "Tarjeta de Regalo: ".$tipoPago[1]. $value;
				}
			}
		}
			//echo $this->sales->payment_type;
		/*
		if($this->sales->save()){
			$this->sales_payments->sales_id = $this->sales->id;
			$this->sales_payments->payment_type = Input::get('payment_type');
			$this->sales_payments->payment_amount = Input::get('tipo')==1 ? Input::get('pay_qty') * -1:Input::get('pay_qty');
			$this->sales_payments->save();
			if((Input::get('data'))){
				$counter = 1;
				foreach (Input::get('data') as $key => $value) {
					$this->sales_items = new ReceivingsItems;
					$this->inventories = new Inventories;
					foreach ($value as $vals => $values) {
						if($vals=='item'){
							$this->sales_items->item_id = $values;
						}elseif($vals=='quantity'){
							$this->items = Items::where('id','=',$this->sales_items->item_id)->first();
							$this->sales_items->sales_id = $this->sales->id;
							$this->sales_items->description = $this->items->description;
							$this->sales_items->serialnumber = $this->items->serialnumber;
							$this->sales_items->line = $counter;
							$this->sales_items->quantity_purchased = Input::get('tipo')==1 ? $values * -1:$values;
							$this->sales_items->item_cost_price = $this->items->cost_price;
							$this->sales_items->item_unit_price = $this->items->unit_price;
							$this->sales_items->discount_percent = '0';
							$this->sales_items->item_location = '1';
							##
							##
							$this->inventories = new Inventories;
							$this->inventories->item_id = $this->sales_items->item_id;
							$this->inventories->user_id = Auth::user()->id;
							$this->inventories->comment = Input::get('tipo')==1 ? 'Dev # '.$this->sales->id : 'Rec # '.$this->sales->id;
							$this->inventories->location = '1';
							$this->inventories->inventory = $this->sales_items->quantity_purchased;
							$this->inventories->save();
							##
							##
							$this->item_quantities = ItemQuantities::where('item_id','=',$this->sales_items->item_id)->first();
							$this->item_quantities->quantity = $this->item_quantities->quantity + $this->sales_items->quantity_purchased;
							$this->item_quantities->save();
							$counter += 1;
						}
					$this->sales_items->sales_id = $this->sales->id;
					$this->sales_items->save();


					}
				}
			}
			return Redirect::to('pos/sales/' . $this->sales->id . '/receipt')->with('success', 'Se ha generado la venta con éxito');
		}*/
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
		->select(array('item_kit_items.quantity as kitqty','items.id','items.name','items.item_number','items.description','items.unit_price','item_quantities.quantity','items.is_serialized'))
		->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->unit_price, 'qty' => $query->quantity, 'kitqty' => $query->kitqty, 'is_serialized' => $query->is_serialized ];
		}
		return Response::json($results);
	}

	public function getAutocompleteitem(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('items')
		->distinct()
		->leftjoin('item_quantities','item_quantities.item_id','=','items.id')
		->select(array('items.id','items.name','items.item_number','items.description','items.cost_price','item_quantities.quantity','items.is_serialized'))
		->where('items.id', '=', $term)
		->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->cost_price, 'qty' => $query->quantity, 'is_serialized' => $query->is_serialized ];
		}
		return Response::json($results);
	}

	public function getGiftcardsnumbers()
	{
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('giftcards')
		->distinct()
		->leftjoin('peoples','giftcards.people_id','=','peoples.id')
		->select(array('giftcards.id','giftcards.number','giftcards.value','giftcards.deleted','peoples.first_name','peoples.last_name'))
		->where('number','=',$term)
		->get();
		foreach ($queries as $query)
		{
			$results[] = [
				'id' => $query->id,
				'number' => $query->number,
				'value' => $query->value,
				'deleted' => $query->deleted,
				'first_name' => $query->first_name,
				'last_name' => $query->last_name
			];
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
