<?php

class ReceivingsController extends PosDashboardController {

	/**
	* Customers Model
	* @var Receivings
	*/
	protected $receivings;

	/**
	* Inject the models.
	* @param Receivings $receivings
	*/
	public function __construct(Receivings $receivings)
	{
		parent::__construct();
		$this->receivings = $receivings;
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
				'data' => Input::get('data')
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
				'comment' => 'min:3',
				'data' => 'required|array'
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
			return Redirect::to('pos/receivings')->withErrors($messages);
		}
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
