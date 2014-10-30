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


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
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
		$queries = DB::table('items')
				->distinct()
				->where('name', 'LIKE', '%'.$term.'%')
				->orWhere('item_number', 'LIKE', '%'.$term.'%')
				->orWhere('description', 'LIKE', '%'.$term.'%')
				->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'name' => $query->name, 'item_number' => $query->item_number, 'description' => $query->description, 'cost' => $query->cost_price ];
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
