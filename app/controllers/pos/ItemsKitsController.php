<?php

class ItemsKitsController extends PosDashboardController {

	/**
     * Customers Model
     * @var Items
     */
    protected $items_kits;
    protected $item_kit_items;
    protected $items;

    /**
     * Inject the models.
     * @param Customers $suppliers
     */
    public function __construct(ItemsKits $items_kits, ItemKitItems $item_kit_items , Items $items)
    {
        parent::__construct();
        $this->items_kits = $items_kits;
        $this->item_kit_items = $item_kit_items;
        $this->items = $items;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Kits de Artículos';
		return View::make('pos/items_kits/index', compact('title'));
	}

	public function getData(){
		$items = ItemsKits::select(array('id', 'items_kits.name', 'items_kits.description'));

        return Datatables::of($items)
        ->add_column('actions', '<ul class="stack button-group round">
                                    	<li><a href="{{{ URL::to(\'pos/items_kits/\' . $id . \'/edit\' ) }}}" class="iframe button tiny">{{{ Lang::get(\'button.edit\') }}}</a></li>
                                    	<li><a href="{{{ URL::to(\'pos/items_kits/\' . $id . \'/delete\' ) }}}" class="iframe2 button tiny">{{{ Lang::get(\'button.delete\') }}}</a></li>
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
		$title = 'Crear Kit';
		$mode = 'create';
		return View::make('pos/items_kits/create_edit', compact('title','mode'));
	}

	public function postCreate()
	{
		/*echo "<pre>";
		print_r(Input::all());
		echo "<pre>";
		*/
		$this->items_kits->name = Input::get('name');
		$this->items_kits->description = Input::get('description');
		if($this->items_kits->save()){
			foreach (Input::get('data') as $key => $value) {
				$this->item_kit_items = new ItemKitItems;
				foreach ($value as $vals => $values) {
					if($vals=='item'){
						$this->item_kit_items->item_id = $values;
					}elseif($vals=='quantity'){
						$this->item_kit_items->quantity = $values;
					}
				$this->item_kit_items->items_kits_id = $this->items_kits->id;
				$this->item_kit_items->save();
				}
			}
			if ($this->item_kit_items->id){
				return Redirect::to('pos/items_kits/' . $this->items_kits->id . '/edit')->with('success', 'Se ha creado el artículo con éxito');
			}
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
	public function getEdit($items_kits)
	{

		if($items_kits->id)
		{
			$item_kit_items = ItemKitItems::where('items_kits_id','=',$items_kits->id)->get();
			$title = "Kit de Artículos";
			$mode = "edit";
			return View::make('pos/items_kits/create_edit', compact('items_kits', 'title', 'mode', 'item_kit_items'));
		}
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
				->orWhere('description', 'LIKE', '%'.$term.'%')
				->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'id' => $query->id, 'value' => $query->name, 'desc' => $query->description ];
		}
		return Response::json($results);
	}


}
