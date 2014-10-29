<?php

class CustomersController extends PosDashboardController {

    /**
     * Customers Model
     * @var Customers
     * @var People
     */
    protected $customers;
    protected $people;

    /**
     * Inject the models.
     * @param Customers $customers
     */
    public function __construct(Customers $customers, Peoples $people)
    {
        parent::__construct();
        $this->customers = $customers;
        $this->people = $people;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Clientes';
		return View::make('pos/customers/index',compact('title'));
	}

	public function getData(){
		$customer = Customers::leftjoin('peoples', 'peoples.id', '=', 'customers.people_id')
                    ->select(array('customers.people_id', 'peoples.id', 'peoples.last_name','peoples.first_name','peoples.email','peoples.phone_number'))
                    ->where('customers.deleted','=',0);


        return Datatables::of($customer)
        // ->edit_column('created_at','{{{ Carbon::now()->diffForHumans(Carbon::createFromFormat(\'Y-m-d H\', $test)) }}}')
        ->edit_column('email', '<a href="mailto:{{{ HTML::email($email) }}}">{{{ HTML::email($email) }}}</a>')
        ->add_column('actions', '<a href="{{{ URL::to(\'pos/customers/\' . $people_id . \'/edit\' ) }}}" class="iframe button tiny">{{{ Lang::get(\'button.edit\') }}}</a>
                                    <a href="{{{ URL::to(\'pos/customers/\' . $people_id . \'/delete\' ) }}}" class="iframe button tiny alert">{{{ Lang::get(\'button.delete\') }}}</a>
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
		$title = "Crear Cliente";
		$mode = "create";

		return View::make('pos/customers/create_edit', compact('title','mode'));
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
			$this->customers->account_number = Input::get('account');
			$this->customers->people_id = $this->people->id;
            $this->customers->taxable = Input::get('taxable') ? Input::get('taxable') : 0;
			$this->customers->save();
			if($this->customers->id){
				// Redirect to the new user page
            	return Redirect::to('pos/customers/' . $this->people->id . '/edit')->with('success', 'Se ha creado el cliente con éxito');
			}
		}
	}

	/**
     * Show the form for editing the specified resource.
     *
     * @param $user
     * @return Response
     */
    public function getEdit($people)
    {
        if ( $people->id )
        {

        	$customers = Customers::where('people_id','=',$people->id)->first();

            // Title
            $title = 'Clientes';
            // mode
            $mode = 'edit';

            return View::make('pos/customers/create_edit', compact('people', 'customers', 'title', 'mode'));
        }
        else
        {
            return Redirect::to('pos/customers')->with('error', 'El cliente no existe');
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
			$customers = Customers::where('people_id','=',$people->id)->first();
			$customers->account_number = Input::get('account');
            Input::get('taxable') ? $customers->taxable = 1 :  $customers->taxable = 0;

			if($customers->save()){
				// Redirect to the new user page
            	return Redirect::to('pos/customers/' . $people->id . '/edit')->with('success', 'Se ha guardado el cliente con éxito');
			}else{
				return Redirect::to('pos/customers/' . $people->id . '/edit')->with('error', 'No se ha podido guardar el cliente, Intente nuevamente');
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
        $title = 'Borrar Cliente';

        // Show the page
        return View::make('pos/customers/delete', compact('people', 'title'));
	}

	public function postDelete($people)
	{
		$customers = Customers::where('people_id','=',$people->id)->first();
		$customers->deleted = 1;
		$customers->save();
	}


}
