<?php

class ItemsController extends PosDashboardController {

	/**
     * Customers Model
     * @var Items
     */
    protected $items;

    /**
     * Inject the models.
     * @param Customers $suppliers
     */
    public function __construct(Items $items)
    {
        parent::__construct();
        $this->items = $items;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Articulos';
		return View::make('pos/items/index', compact('title'));
	}

	public function getData(){
		$items = Items::leftjoin('item_quantities', 'items.id', '=', 'item_quantities.item_id')
					->leftjoin('items_taxes', 'items.id', '=', 'items_taxes.item_id')
                    ->select(array('items.id', 'items.item_number', 'items.name', 'items.category', 'items.cost_price', 'items.unit_price', 'item_quantities.quantity', 'items_taxes.percent'))
                    ->where('items.deleted','=',0);


        return Datatables::of($items)
        ->add_column('inventory', '<a href="{{{ URL::to(\'pos/suppliers/\' . $people_id . \'/edit\' ) }}}" class="iframe button tiny">{{{ Lang::get(\'button.edit\') }}}</a>
                                    <a href="{{{ URL::to(\'pos/suppliers/\' . $people_id . \'/delete\' ) }}}" class="iframe button tiny alert">{{{ Lang::get(\'button.delete\') }}}</a>
            ')
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
		$title = "Crear Artículo";
		$mode = "create";

		$supplier_options = DB::table('suppliers')
								->where('deleted','=',0)
								->orderBy('company_name', 'asc')
								->lists('company_name','id');

		return View::make('pos/items/create_edit', compact('title','mode','supplier_options'));
	}

	public function postCreate()
	{
		print_r(Input::all());
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
