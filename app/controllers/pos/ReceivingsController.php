<?php

class ReceivingsController extends PosDashboardController {

	/**
	* Receivings Model
	* @var Receivings
	* @var AppConfig
	* @var ReceivingsItems
	* @var ReceivingsPayments
	* @var Items
	* @var ItemsTaxes
	* @var Inventories
	* @var ItemQuantities
	*/
	protected $receivings;
	protected $app_config;
	protected $receivings_items;
	protected $receivings_payments;
	protected $items;
	protected $items_taxes;
	protected $inventories;
	protected $item_quantities;

	/**
	* Inject the models.
	* @param Receivings $receivings
	*/
	public function __construct(AppConfig $app_config, Receivings $receivings, ReceivingsItems $receivings_items, ReceivingsPayments $receivings_payments, Items $items, ItemsTaxes $items_taxes, Inventories $inventories, ItemQuantities $item_quantities)
	{
		parent::__construct();
		$this->app_config = $app_config;
		$this->receivings = $receivings;
		$this->receivings_items = $receivings_items;
		$this->receivings_payments = $receivings_payments;
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
		$title = "Entrada de Artículos";
		return View::make('pos/receivings/index', compact('title'));
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
			'data.required' => 'Debe tener al menos un articulo'
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
			'supplier_id' => Input::get('supplier_id'),
			'user_id' => Auth::user()->id,
			'comment' => Input::get('comment'),
			'payment_type' => Input::get('payment_type'),
			'data' => Input::get('data'),
			'pay_qty' => Input::get('pay_qty')
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
			'comment' => 'min:3',
			'data' => 'required|array',
			'pay_qty' => 'numeric'
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
			return Redirect::to('pos/receivings')
							->withErrors($messages)
							->withInput();
		}

		$this->receivings->supplier_id = Input::get('supplier_id');
		$this->receivings->user_id = Auth::user()->id;
		$this->receivings->comment = Input::get('comment');
		$this->receivings->payment_type = Input::get('payment_type');
		if($this->receivings->save()){
			$this->receivings_payments->receivings_id = $this->receivings->id;
			$this->receivings_payments->payment_type = Input::get('payment_type');
			$this->receivings_payments->payment_amount = Input::get('tipo')==1 ? Input::get('pay_qty') * -1:Input::get('pay_qty');
			$this->receivings_payments->save();
			if((Input::get('data'))){
				$counter = 1;
				foreach (Input::get('data') as $key => $value) {
					$this->receivings_items = new ReceivingsItems;
					$this->inventories = new Inventories;
					foreach ($value as $vals => $values) {
						if($vals=='item'){
							$this->receivings_items->item_id = $values;
						}elseif($vals=='quantity'){
							$this->items = Items::where('id','=',$this->receivings_items->item_id)->first();
							$this->receivings_items->receivings_id = $this->receivings->id;
							$this->receivings_items->description = $this->items->description;
							$this->receivings_items->serialnumber = $this->items->serialnumber;
							$this->receivings_items->line = $counter;
							$this->receivings_items->quantity_purchased = Input::get('tipo')==1 ? $values * -1:$values;
							$this->receivings_items->item_cost_price = $this->items->cost_price;
							$this->receivings_items->item_unit_price = $this->items->unit_price;
							$this->receivings_items->discount_percent = '0';
							$this->receivings_items->item_location = '1';
							##
							##
							$this->inventories = new Inventories;
							$this->inventories->item_id = $this->receivings_items->item_id;
							$this->inventories->user_id = Auth::user()->id;
							$this->inventories->comment = Input::get('tipo')==1 ? 'Dev # '.$this->receivings->id : 'Rec # '.$this->receivings->id;
							$this->inventories->location = '1';
							$this->inventories->inventory = $this->receivings_items->quantity_purchased;
							$this->inventories->save();
							##
							##
							$this->item_quantities = ItemQuantities::where('item_id','=',$this->receivings_items->item_id)->first();
							$this->item_quantities->quantity = $this->item_quantities->quantity + $this->receivings_items->quantity_purchased;
							$this->item_quantities->save();
							$counter += 1;
						}
					$this->receivings_items->receivings_id = $this->receivings->id;
					$this->receivings_items->save();


					}
				}
			}
			return Redirect::to('pos/receivings/' . $this->receivings->id . '/receipt')->with('success', 'Se ha creado el Kit con éxito');
		}

	}

	public function getReceipt($receivings)
	{
		$storeName = AppConfig::where('key','=','company')->select('value')->first();
		$storeAddress = AppConfig::where('key','=','address')->select('value')->first();
		$storePhone = AppConfig::where('key','=','phone')->select('value')->first();
		$storeEmail = AppConfig::where('key','=','email')->select('value')->first();
		$storeWww = AppConfig::where('key','=','website')->select('value')->first();
		$supplier = Suppliers::where('id','=',$receivings->supplier_id)->first();
		if($supplier){
			$people = Peoples::where('id','=',$supplier->people_id)->first();
		}
		else{
			$people = "Mostrador";
		}
		$receivings_items = ReceivingsItems::leftjoin('items','receivings_items.item_id','=','items.id')
											->select(array('receivings_items.quantity_purchased','items.name','receivings_items.description','receivings_items.item_cost_price','receivings_items.serialnumber'))
											->where('receivings_id','=',$receivings->id)
											->orderBy('line')
											->get();
		return View::make('pos/receivings/receipt', compact(array('receivings','storeName','storeAddress','storePhone','storeEmail','storeWww','supplier','people','receivings_items')));
	}

	public function getAutocomplete(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('items')
		->distinct()
		->leftjoin('item_quantities','item_quantities.item_id','=','items.id')
		->select(array('items.id','items.name','items.item_number','items.description','items.cost_price','item_quantities.quantity'))
		->where('name', 'LIKE', '%'.$term.'%')
		->orWhere('item_number', 'LIKE', '%'.$term.'%')
		->orWhere('description', 'LIKE', '%'.$term.'%')
		->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->cost_price, 'qty' => $query->quantity ];
		}
		return Response::json($results);
	}

	public function getSuppliers(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('suppliers')
		->distinct()
		->where('company_name', 'LIKE', '%'.$term.'%')
		->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'company_name' => $query->company_name ];
		}
		return Response::json($results);
	}


}
