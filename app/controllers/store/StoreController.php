<?php

class StoreController extends PosDashboardController {

	/**
	* StoreOrders Model
	* @var StoreOrders
	* @var StoreOrdersItems
	* @var StoreOrdersItemsTaxes
	* @var AppConfig
	* @var Items
	* @var ItemsTaxes
	* @var Inventories
	* @var ItemQuantities
	*/
	protected $store_orders;
	protected $store_orders_items;
	protected $store_orders_items_taxes;
	protected $app_config;
	protected $items;
	protected $items_taxes;
	protected $inventories;
	protected $item_quantities;

	/**
	* Inject the models.
	* @param StoreOrders $store_orders
	*/
	public function __construct(AppConfig $app_config, StoreOrders $store_orders, StoreOrdersItems $store_orders_items, StoreOrdersItemsTaxes $store_orders_items_taxes, Items $items, ItemsTaxes $items_taxes, Inventories $inventories, ItemQuantities $item_quantities)
	{
		parent::__construct();
		$this->app_config = $app_config;
		$this->store_orders = $store_orders;
		$this->store_orders_items = $store_orders_items;
		$this->store_orders_items_taxes = $store_orders_items_taxes;
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
		$title = 'Tienda';
		$company = AppConfig::all();
		return View::make('store/index', compact('title','company'));
	}

	public function postIndex()
	{
		//var_dump(Input::all());
		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'nombre.required' => 'El campo Nombre es Obligatorio',
			'ap_pat.required' => 'El campo Apellido Paterno es Obligatorio',
			'email.required' => 'El campo Correo Electrónico es Obligatorio',
			'phone.required' => 'El campo Teléfono es Obligatorio',
			'entry.required' => 'Debe tener al menos un articulo',
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
			'comment' => Input::get('comment'),
			'nombre' => Input::get('nombre'),
			'ap_pat' => Input::get('ap_pat'),
			'ap_mat' => Input::get('ap_mat'),
			'email' => Input::get('email'),
			'phone' => Input::get('phone'),
			'entry' => Input::get('entry'),
			'email_company' => Input::get('email-company'),
			'name_company' => Input::get('name-company'),
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
			'comment' => 'min:3',
			'nombre' => 'required',
			'ap_pat' => 'required',
			'email' => 'required',
			'phone' => 'required',
			'entry' => 'required|array',
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make($data,$rules,$messages);

		if($validator->fails()){
			$messages = $validator->messages();
			echo "<hr>";
			echo "<pre>";
				var_dump($messages);
			echo "</pre>";
			return Redirect::to('/store')->withErrors($messages)->withInput();
		}

		Mail::send('emails.welcome', $data, function($message)
		{
		    $message->to(Input::get('email-company'), 
		    Input::get('name-company'))->subject('Pedido en Linea, de '. Input::get('nombre').' '.Input::get('ap_pat').' '.Input::get('ap_mat'));
		});

		$this->store_orders->nombre = Input::get('nombre');
		$this->store_orders->ap_pat = Input::get('ap_pat');
		$this->store_orders->ap_mat = Input::get('ap_mat');
		$this->store_orders->email = Input::get('email');
		$this->store_orders->phone = Input::get('phone');
		if($this->store_orders->save()){
			$this->store_orders_items_taxes->store_order_id = $this->store_orders->id;
			$this->store_orders_items_taxes->item_id = 0;
			$this->store_orders_items_taxes->line = 1;
			$this->store_orders_items_taxes->name = 'IVA';
			$this->store_orders_items_taxes->percent = Input::get('tax');
			$this->store_orders_items_taxes->save();
			#######################################################################
			if((Input::get('entry'))){
				$counter = 1;
				foreach (Input::get('entry') as $key => $value) {
					$this->store_orders_items = new StoreOrdersItems;
					foreach ($value as $vals => $values) {
						if($vals=='item'){
							$this->store_orders_items->item_id = $values;
						}elseif($vals=='serialnumber'){
							$this->store_orders_items->serialnumber = $values;
						}elseif($vals=='desc'){
							$this->store_orders_items->discount_percent = $values;
						}elseif($vals=='quantity'){
							$this->items = Items::where('id','=',$this->store_orders_items->item_id)->first();
							$this->store_orders_items->store_order_id = $this->store_orders->id;
							$this->store_orders_items->description = $this->items->description;
							$this->store_orders_items->line = $counter;
							$this->store_orders_items->quantity_purchased = Input::get('tipo')==1 ? $values * -1:$values;
							$this->store_orders_items->item_cost_price = $this->items->cost_price;
							$this->store_orders_items->item_unit_price = $this->items->unit_price;
							$this->store_orders_items->item_location = '1';
							$counter += 1;
						}
					$this->store_orders_items->store_order_id = $this->store_orders->id;
					$this->store_orders_items->save();
					}
				}
			}
			$nombre = Input::get('nombre');
			$ap_pat = Input::get('ap_pat');
			$ap_mat = Input::get('ap_mat');
			return View::make('store/tnks',compact('nombre','ap_pat','ap_mat'));
		}
	}

	public function getStore(){
		$title = "Pedidos en linea";
		return View::make('pos/store/index',compact('title'));
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
		->select(array('items.id','items.name','items.item_number','items.description','items.unit_price','item_quantities.quantity','items.is_serialized'))
		->where('items.id', '=', $term)
		->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->unit_price, 'qty' => $query->quantity, 'is_serialized' => $query->is_serialized ];
		}
		return Response::json($results);
	}

}
