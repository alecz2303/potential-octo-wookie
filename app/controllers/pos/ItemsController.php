	<?php

class ItemsController extends PosDashboardController {

	/**
     * Customers Model
     * @var Items
     */
    protected $items;
    protected $items_taxes;
    protected $inventories;
    protected $item_quantities;

    /**
     * Inject the models.
     * @param Customers $suppliers
     */
    public function __construct(Items $items, ItemsTaxes $items_taxes, Inventories $inventories, ItemQuantities $item_quantities)
    {
        parent::__construct();
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
		$title = 'Articulos';
		return View::make('pos/items/index', compact('title'));
	}

	public function getData(){
		$items = Items::leftjoin('item_quantities', 'items.id', '=', 'item_quantities.item_id')
					->leftjoin('items_taxes', 'items.id', '=', 'items_taxes.item_id')
                    ->select(array('items.id', 'items.item_number', 'items.name', 'items.category', 'items.cost_price', 'items.unit_price', 'item_quantities.quantity', 'items_taxes.percent'))
                    ->where('items.deleted','=',0);


        return Datatables::of($items)
        ->add_column('inventory', '<ul class="stack button-group round">
        						   		<li><a href="{{{ URL::to(\'pos/items/\' . $id . \'/inventory\' ) }}}" class="iframe1 button tiny">Inv.</a></li>
                                    	<li><a href="{{{ URL::to(\'pos/items/\' . $id . \'/detail\' ) }}}" class="iframe1 button tiny">Det.</a></li>
                                   </ul>
            ')
        ->add_column('actions', '
		<ul class="stack button-group round">
			<li><a href="{{{ URL::to(\'pos/items/\' . $id . \'/delete\' ) }}}" class="iframe2 button tiny alert">{{{ Lang::get(\'button.delete\') }}}</a></li>
            <li><a href="{{{ URL::to(\'pos/items/\' . $id . \'/edit\' ) }}}" class="iframe button tiny">{{{ Lang::get(\'button.edit\') }}}</a></li>
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
		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'item_number.unique' => 'El UPC/EAN/ISBN: '.Input::get('item_number').' ya esta siendo utilizado',
			'name.unique' => 'El Nombre del Artículo: '.Input::get('name').' ya esta siendo utilizado',
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
			'item_number' => Input::get('item_number'),
			'name' => Input::get('name'),
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
			'item_number' => 'unique:items',
			'name' => 'unique:items',
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make($data,$rules,$messages);

		if($validator->fails()){
			return Redirect::to('pos/items/create')
							->withErrors($messages)
							->withInput();
		}
		##########################################
		$this->items->name = Input::get('name');
		$this->items->category = Input::get('category');
		$this->items->supplier_id = Input::get('supplier_id');
		$this->items->item_number = Input::get('item_number');
		$this->items->description = Input::get('description');
		$this->items->cost_price = Input::get('cost_price');
		$this->items->unit_price = Input::get('unit_price');
		$this->items->quantity = 0;
		$this->items->reorder_level = Input::get('reorder_level');
		$this->items->is_serialized = Input::get('is_serialized') ? Input::get('is_serialized') : 0;
		$this->items->deleted = Input::get('deleted') ? Input::get('deleted') : 0;
		/*
		$this->items->custom1 = Input::get('custom1');
		$this->items->custom2 = Input::get('custom2');
		$this->items->custom3 = Input::get('custom3');
		$this->items->custom4 = Input::get('custom4');
		$this->items->custom5 = Input::get('custom5');
		$this->items->custom6 = Input::get('custom6');
		$this->items->custom7 = Input::get('custom7');
		$this->items->custom8 = Input::get('custom8');
		$this->items->custom9 = Input::get('custom9');
		$this->items->custom10 = Input::get('custom10');
		*/
		if($this->items->save()){
			$this->inventories->item_id = $this->items->id;
			$this->inventories->user_id = Auth::user()->id;
			$this->inventories->comment = 'Edición Manual de Cantidad';
			$this->inventories->location = '1';
			$this->inventories->inventory = Input::get('quantity');

			$this->items_taxes->item_id = $this->items->id;
			$this->items_taxes->name = Input::get('items_taxes_name');
			$this->items_taxes->percent = Input::get('items_taxes_percent');

			$this->item_quantities->item_id = $this->items->id;
			$this->item_quantities->location_id = '1';
			$this->item_quantities->quantity = Input::get('quantity');

			$this->inventories->save();
			$this->items_taxes->save();
			$this->item_quantities->save();

			if ($this->inventories->id && $this->items_taxes->id && $this->item_quantities->id){
				return Redirect::to('pos/items/' . $this->items->id . '/edit')->with('success', 'Se ha creado el artículo con éxito');
			}
		}
	}

	/**
     * Show the form for editing the specified resource.
     *
     * @param $items
     * @return Response
     */
    public function getEdit($items)
    {
        if ( $items->id )
        {

        	$supplier_options = DB::table('suppliers')
								->where('deleted','=',0)
								->orderBy('company_name', 'asc')
								->lists('company_name','id');

        	//$customers = Customers::where('items_id','=',$items->id)->first();
			$items_taxes = ItemsTaxes::where('item_id','=',$items->id)->first();
			$item_quantities = ItemQuantities::where('item_id','=',$items->id)->first();

            // Title
            $title = 'Artículos';
            // mode
            $mode = 'edit';

            return View::make('pos/items/create_edit', compact('items', 'title', 'mode', 'supplier_options', 'items_taxes', 'item_quantities'));
        }
        else
        {
            return Redirect::to('pos/customers')->with('error', 'El cliente no existe');
        }
    }

    public function postEdit($items)
    {
    	#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'item_number.unique' => 'El UPC/EAN/ISBN: '.Input::get('item_number').' ya esta siendo utilizado',
			'name.unique' => 'El Nombre del Artículo: '.Input::get('name').' ya esta siendo utilizado',
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
			'item_number' => Input::get('item_number'),
			'name' => Input::get('name'),
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
			'item_number' => 'unique:items,item_number,'.$items->id,
			'name' => 'unique:items,name,'.$items->id,
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make($data,$rules,$messages);

		if($validator->fails()){
			return Redirect::to('pos/items/create')
							->withErrors($messages)
							->withInput();
		}
		##########################################

    	$items_taxes = ItemsTaxes::where('item_id','=',$items->id)->first();
    	$item_quantities = ItemQuantities::where('item_id','=',$items->id)->first();

    	$oldItems = clone $items;
    	$items->name = Input::get('name');
		$items->category = Input::get('category');
		$items->supplier_id = Input::get('supplier_id');
		$items->item_number = Input::get('item_number');
		$items->description = Input::get('description');
		$items->cost_price = Input::get('cost_price');
		$items->unit_price = Input::get('unit_price');
		$items->quantity = 0;
		$items->reorder_level = Input::get('reorder_level');
		$items->is_serialized = Input::get('is_serialized') ? 1 : 0;
		$items->deleted = Input::get('deleted') ? 1 : 0;
		/*
		$items->custom1 = Input::get('custom1');
		$items->custom2 = Input::get('custom2');
		$items->custom3 = Input::get('custom3');
		$items->custom4 = Input::get('custom4');
		$items->custom5 = Input::get('custom5');
		$items->custom6 = Input::get('custom6');
		$items->custom7 = Input::get('custom7');
		$items->custom8 = Input::get('custom8');
		$items->custom9 = Input::get('custom9');
		$items->custom10 = Input::get('custom10');
		*/
		if($items->save()){
			if($item_quantities->quantity != Input::get('quantity')){
				$newQty = Input::get('quantity')-$item_quantities->quantity;
				$this->inventories->item_id = $items->id;
				$this->inventories->user_id = Auth::user()->id;
				$this->inventories->comment = 'Edición Manual de Cantidad';
				$this->inventories->location = '1';
				$this->inventories->inventory = $newQty;
				$item_quantities->quantity = $item_quantities->quantity + $newQty;
			}

			$items_taxes->name = Input::get('items_taxes_name');
			$items_taxes->percent = Input::get('items_taxes_percent');


			$this->inventories->save();
			$items_taxes->save();
			$item_quantities->save();

			if ($this->inventories->id && $items_taxes->id && $item_quantities->id){
				return Redirect::to('pos/items/' . $items->id . '/edit')->with('success', 'Se han guardado los cambios con éxito');
			}
		}
    }

    public function getDetail($items)
    {
    	$item_quantities = ItemQuantities::where('item_id','=',$items->id)->first();
    	$inventory = (Inventories::leftjoin('users','inventories.user_id','=','users.id')
    								->select(array('inventories.created_at','users.username','inventories.inventory','inventories.comment'))
    								->where('inventories.item_id','=',$items->id)
    								->orderBy('inventories.created_at', 'desd')
    								->get());

    	$title = 'Detalles';
    	return View::make('pos/items/detail',compact('items', 'title', 'item_quantities', 'inventory'));


    }

    public function getInventory($items)
    {
    	$item_quantities = ItemQuantities::where('item_id','=',$items->id)->first();

    	$title = 'Detalles';
    	return View::make('pos/items/inventory',compact('items', 'title', 'item_quantities'));
    }

    public function postInventory($items)
    {
    	$item_quantities = ItemQuantities::where('item_id','=',$items->id)->first();

    	$this->inventories->item_id = $items->id;
		$this->inventories->user_id = Auth::user()->id;
		$this->inventories->comment = Input::get('comment') ? Input::get('comment') : "";
		$this->inventories->location = '1';
		$this->inventories->inventory = Input::get('quantity');
		$item_quantities->quantity = $item_quantities->quantity + Input::get('quantity');

		if($this->inventories->save() && $item_quantities->save()){
			return Redirect::to('pos/items/' . $items->id . '/inventory')->with('success', 'Se han guardado los cambios con éxito');
		}else{
			return Redirect::to('pos/items/' . $items->id . '/inventory')->with('error', 'Ha ocurrido un error, intente nuevamente');
		}
    }

    public function getDelete($items)
    {
    	// Title
        $title = 'Borrar Artículo';

        // Show the page
        return View::make('pos/items/delete', compact('items', 'title'));
    }

    public function postDelete($items)
    {
    	$items = Items::where('id','=',$items->id)->first();
    	$items->deleted = 1;
    	$items->save();
    }


	public function getAutocomplete(){
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('items')
				->distinct()
				->select(array('category'))
				->where('category', 'LIKE', '%'.$term.'%')
				//->orWhere('last_name', 'LIKE', '%'.$term.'%')
				->take(5)->get();
		foreach ($queries as $query)
		{
			$results[] = [ 'value' => $query->category ];
		}
		return Response::json($results);
	}

}
