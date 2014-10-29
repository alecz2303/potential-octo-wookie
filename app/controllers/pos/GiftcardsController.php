<?php

class GiftcardsController extends PosDashboardController {

	/**
	* Customers Model
	* @var giftcards
	*/
	protected $giftcards;



	/**
	* Inject the models.
	* @param Customers $suppliers
	*/
	public function __construct(Giftcards $giftcards)
	{
		parent::__construct();
		$this->giftcards = $giftcards;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Tarjetas de Regalo';
    	return View::make('pos/giftcards/index', compact(array('title')));
	}

	public function getData(){
		$giftcards = Giftcards::leftjoin('peoples', 'peoples.id', '=', 'giftcards.people_id')
								  ->select(array('giftcards.id','peoples.last_name','peoples.first_name','giftcards.number','giftcards.value'))
								  ->where('giftcards.deleted','=',0);

		return Datatables::of($giftcards)
		->add_column('actions', '<ul class="stack button-group round">
									<li><a href="{{{ URL::to(\'pos/giftcards/\' . $id . \'/edit\' ) }}}" class="iframe button tiny">{{{ Lang::get(\'button.edit\') }}}</a></li>
									<li><a href="{{{ URL::to(\'pos/giftcards/\' . $id . \'/delete\' ) }}}" class="iframe2 button alert tiny">{{{ Lang::get(\'button.delete\') }}}</a></li>
								</ul>
			')
		->remove_column('id')

		->make();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$title = 'Crear Tarjeta de Regalo';
		$mode = 'create';
		return View::make('pos/giftcards/create_edit', compact('title','mode'));
	}

	public function postCreate()
	{
		echo "<pre>";
		print_r(Input::all());
		echo "</pre>";

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
				'name' => 'required',
				'people_id' => 'required',
				'number'=>'integer|required|unique:giftcards',
				'value'=>'numeric|required'
		);

		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'name.required'=>'Seleccione un nombre de la lista',
			'people_id.required'=>'Seleccione un nombre de la lista',
			'number.unique'=>'El número :values ya esta siendo usado'
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make(Input::all(),$rules,$messages);
		if($validator->fails()){
			$messages = $validator->messages();
			return Redirect::to('pos/giftcards/create')->withErrors($messages);
		}

		$this->giftcards->number = Input::get('number');
		$this->giftcards->value = Input::get('value');
		$this->giftcards->people_id = Input::get('people_id');
		$this->giftcards->deleted = 0;

		if($this->giftcards->save()){
			return Redirect::to('pos/giftcards/' . $this->giftcards->id . '/edit')->with('success', 'Se ha creado la Tarjeta de Regalo con éxito');
		}else{
			return Redirect::to('pos/giftcards/create')->withErrors();
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function getAutocomplete(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('peoples')
				->distinct()
				->where('first_name', 'LIKE', '%'.$term.'%')
				->orWhere('last_name', 'LIKE', '%'.$term.'%')
				->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'first_name' => $query->first_name, 'last_name' => $query->last_name ];
		}
		return Response::json($results);
	}


}
