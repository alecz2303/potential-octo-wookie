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
			'number.unique'=>'El número de Tarjeta ya esta siendo usado'
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

	public function getEdit($giftcards)
	{
		if($giftcards->id){

			$peoples = Peoples::where('peoples.id','=',$giftcards->people_id)->first();
			$title = 'Edición de Tarjetas de Regalo';
			$mode = 'edit';

			return View::make('pos/giftcards/create_edit', compact(array('giftcards','peoples','title','mode')));
		}else{
			return Redirect::to('pos/giftcards')->withErrors('El cliente no existe');
		}
	}

	public function postEdit($giftcards)
	{
		$oldGiftcards = clone $giftcards;
		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
				'name' => 'required',
				'people_id' => 'required',
				'number'=>'integer|required|unique:giftcards,number,'.$giftcards->id,
				'value'=>'numeric|required'
		);

		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'name.required'=>'Seleccione un nombre de la lista',
			'people_id.required'=>'Seleccione un nombre de la lista',
			'number.unique'=>'El número de Tarjeta ya esta siendo usado'
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make(Input::all(),$rules,$messages);
		if($validator->fails()){
			$messages = $validator->messages();
			return Redirect::to('pos/giftcards/'.$giftcards->id.'/edit')->withErrors($messages);
		}

		$giftcards->number = Input::get('number');
		$giftcards->value = Input::get('value');
		$giftcards->people_id = Input::get('people_id');
		$giftcards->deleted = 0;
		if($giftcards->save()){
			return Redirect::to('pos/giftcards/' . $giftcards->id . '/edit')->with('success', 'Se ha editado la Tarjeta de Regalo con éxito');
		}else{
			return Redirect::to('pos/giftcards/' . $giftcards->id . '/edit')->withErrors();
		}
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

	public function getDelete($giftcards)
	{
		if($giftcards->id){

			$peoples = Peoples::where('peoples.id','=',$giftcards->people_id)->first();
			$title = 'Borrar de Tarjeta de Regalo';

			return View::make('pos/giftcards/delete', compact(array('giftcards','peoples','title')));
		}else{
			return Redirect::to('pos/giftcards')->withErrors('El cliente no existe');
		}
	}

	public function postDelete($giftcards)
	{
		$giftcards = Giftcards::where('id','=',$giftcards->id)->first();
		$giftcards->deleted = 1;
		$giftcards->save();
	}

}
