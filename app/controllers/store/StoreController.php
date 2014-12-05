<?php

class StoreController extends \BaseController {

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
		var_dump(Input::all());
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
			'email' => Input::get('email'),
			'phone' => Input::get('phone'),
			'entry' => Input::get('entry'),
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
		    $message->to('alejandrorueda2303@gmail.com', 'Alejandro Rueda')->subject('Welcome!');
		});
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
