<?php

class SuppliersController extends PosDashboardController {

	/**
     * Customers Model
     * @var Suppliers
     * @var People
     */
    protected $suppliers;
    protected $people;

    /**
     * Inject the models.
     * @param Customers $suppliers
     */
    public function __construct(Suppliers $suppliers, Peoples $people)
    {
        parent::__construct();
        $this->suppliers = $suppliers;
        $this->people = $people;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Proveedores';
		return View::make('pos/suppliers/index', compact('title'));
	}

	public function getData(){
		$customer = Suppliers::leftjoin('peoples', 'peoples.id', '=', 'suppliers.people_id')
                    ->select(array('suppliers.people_id', 'peoples.id', 'suppliers.company_name', 'peoples.last_name','peoples.first_name','peoples.email','peoples.phone_number'))
                    ->where('suppliers.deleted','=',0);


        return Datatables::of($customer)
        // ->edit_column('created_at','{{{ Carbon::now()->diffForHumans(Carbon::createFromFormat(\'Y-m-d H\', $test)) }}}')
        ->edit_column('email', '<a href="mailto:{{{ HTML::email($email) }}}">{{{ HTML::email($email) }}}</a>')
        ->add_column('actions', '<a href="{{{ URL::to(\'pos/suppliers/\' . $people_id . \'/edit\' ) }}}" class="iframe button tiny">{{{ Lang::get(\'button.edit\') }}}</a>
                                    <a href="{{{ URL::to(\'pos/suppliers/\' . $people_id . \'/delete\' ) }}}" class="iframe button tiny alert">{{{ Lang::get(\'button.delete\') }}}</a>
            ')

        ->remove_column('id')
        ->remove_column('people_id')

        ->make();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$title = "Crear Proveedor";
		$mode = "create";

		return View::make('pos/suppliers/create_edit', compact('title','mode'));
	}

	public function postCreate()
	{
		$this->people->first_name = Input::get('first_name');
		$this->people->last_name = Input::get('last_name');
		$this->people->phone_number = Input::get('phone_number');
		$this->people->email = Input::get('email');
		$this->people->address_1 = Input::get('address_1');
		$this->people->address_2 = Input::get('address_2');
		$this->people->city = Input::get('city');
		$this->people->state = Input::get('state');
		$this->people->zip = Input::get('zip');
		$this->people->country = Input::get('country');
		$this->people->comments = Input::get('comments');
		$this->people->save();

		if($this->people->id){
			$this->suppliers->account_number = Input::get('account');
			$this->suppliers->company_name = Input::get('company_name');
			$this->suppliers->people_id = $this->people->id;
			$this->suppliers->save();
			if($this->suppliers->id){
				// Redirect to the new user page
            	return Redirect::to('pos/suppliers/' . $this->people->id . '/edit')->with('success', 'Se ha creado el proveedor con éxito');
			}
		}
	}

	public function getEdit($people)
    {
        if ( $people->id )
        {

        	$suppliers = Suppliers::where('people_id','=',$people->id)->first();

            // Title
            $title = 'Proveedores';
            // mode
            $mode = 'edit';

            return View::make('pos/suppliers/create_edit', compact('people', 'suppliers', 'title', 'mode'));
        }
        else
        {
            return Redirect::to('pos/suppliers')->with('error', 'El proveedor no existe');
        }
    }

    public function postEdit($people){
    	$oldPeople = clone $people;
    	$people->first_name = Input::get('first_name');
		$people->last_name = Input::get('last_name');
		$people->phone_number = Input::get('phone_number');
		$people->email = Input::get('email');
		$people->address_1 = Input::get('address_1');
		$people->address_2 = Input::get('address_2');
		$people->city = Input::get('city');
		$people->state = Input::get('state');
		$people->zip = Input::get('zip');
		$people->country = Input::get('country');
		$people->comments = Input::get('comments');

		if($people->save()){
			$suppliers = Suppliers::where('people_id','=',$people->id)->first();
			$suppliers->account_number = Input::get('account');
			$suppliers->company_name = Input::get('company_name');

			if($suppliers->save()){
				// Redirect to the new user page
            	return Redirect::to('pos/suppliers/' . $people->id . '/edit')->with('success', 'Se ha guardado el proveedor con éxito');
			}else{
				return Redirect::to('pos/suppliers/' . $people->id . '/edit')->with('error', 'No se ha podido guardar el proveedor, Intente nuevamente');
			}
		}
    }

    /**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDelete($people)
	{
		// Title
        $title = 'Borrar Proveedor';
        $suppliers = Suppliers::where('people_id','=',$people->id)->first();
        // Show the page
        return View::make('pos/suppliers/delete', compact('suppliers', 'people', 'title'));
	}

	public function postDelete($people)
	{
		$suppliers = Suppliers::where('people_id','=',$people->id)->first();
		$suppliers->deleted = 1;
		$suppliers->save();
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


}
