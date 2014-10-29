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
                                	<li><a href="{{{ URL::to(\'pos/items_kits/\' . $id . \'/delete\' ) }}}" class="iframe2 button alert tiny">{{{ Lang::get(\'button.delete\') }}}</a></li>
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
		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'name.required'=> 'El nombre del Kit es requerido.',
		    'name.min'    => 'El nombre del Kit debe de tener al menos 3 caracteres',
		    'name.unique' => 'El nombre del Kit ya esta siendo usado',
		    'description.required' => 'La descripcion del artículo es requerido',
		    'description.min' => 'La descripcion debe de tener al menos 3 caracteres',
		    'data.required' => 'Debe tener al menos un articulo'
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
				'name' => Input::get('name'),
				'description' => Input::get('description'),
				'data' => Input::get('data')
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
				'name' => 'required|min:3|unique:items_kits',
				'description' => 'min:3',
				'data' => 'required|array'
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make($data,$rules,$messages);



		if($validator->fails()){
			$messages = $validator->messages();
			return Redirect::to('pos/items_kits/create')->withErrors($messages);
		}

		$this->items_kits->name = Input::get('name');
		$this->items_kits->description = Input::get('description');
		if($this->items_kits->save()){
			if((Input::get('data'))){
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
			}
			if ($this->item_kit_items->id){
				return Redirect::to('pos/items_kits/' . $this->items_kits->id . '/edit')->with('success', 'Se ha creado el Kit con éxito');
			}
		}
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
			$item_kit_items = ItemKitItems::leftjoin('items','items.id','=','item_kit_items.item_id')
											->select(array('item_kit_items.id','items.name','item_kit_items.quantity','item_kit_items.item_id'))
											->where('item_kit_items.items_kits_id','=',$items_kits->id)->get();
			$title = "Kit de Artículos";
			$mode = "edit";
			return View::make('pos/items_kits/create_edit', compact('items_kits', 'title', 'mode', 'item_kit_items'));
		}
	}

	public function postEdit($items_kits)
	{
		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'name.required'=> 'El nombre del Kit es requerido.',
		    'name.min'    => 'El nombre del Kit debe de tener al menos 3 caracteres',
		    'name.unique' => 'El nombre del Kit ya esta siendo usado',
		    'description.required' => 'La descripcion del artículo es requerido',
		    'description.min' => 'La descripcion debe de tener al menos 3 caracteres',
		    'data.required' => 'Debe tener al menos un articulo'
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
				'name' => Input::get('name'),
				'description' => Input::get('description'),
				'data' => Input::get('data')
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
				'name' => 'required|min:3|unique:items_kits,name,'.$items_kits->id,
				'description' => 'min:3',
				'data' => 'required|array'
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make($data,$rules,$messages);



		if($validator->fails()){
			$messages = $validator->messages();
			return Redirect::to('pos/items_kits/' . $items_kits->id . '/edit')->withErrors($messages);
		}

		$items_kits->name = Input::get('name');
		$items_kits->description = Input::get('description');
		if($items_kits->save()){
			$error=0;
		}


		$item_kit_items = ItemKitItems::leftjoin('items','items.id','=','item_kit_items.item_id')
											->select(array('item_kit_items.id','items.name','item_kit_items.quantity','item_kit_items.item_id'))
											->where('item_kit_items.items_kits_id','=',$items_kits->id)->get();


		foreach (Input::get('data') as $key => $value) {
			foreach ($value as $vals => $values) {
				if($vals=='item'){
					$item_array[] = $values;
				}elseif($vals=='quantity'){
					$quantity_array[] = $values;
				}
			}
		}
		$counter = 0;
		foreach ($item_array as $key => $value) {
			$counter += 1;
		}

		foreach ($item_kit_items as $key => $value) {
			$item_array_old[] = $value['item_id'];
		}

		$result_add=array_diff($item_array,$item_array_old);
		print_r($result_add);

		$result_del=array_diff($item_array_old,$item_array);
		print_r($result_del);

		foreach ($result_add as $key) {
			$clave = array_search($key, $item_array);
			$this->item_kit_items = new ItemKitItems;
			$this->item_kit_items->items_kits_id = $items_kits->id;
			$this->item_kit_items->item_id = $item_array[$clave];
			$this->item_kit_items->quantity = $quantity_array[$clave];
			if($this->item_kit_items->save()){
				$error = 0;
			}else{
				$error = 1;
			}
		}

		foreach ($result_del as $key) {
			$get_data1 = ItemKitItems::where('item_kit_items.item_id','=',$key)
									  ->where('item_kit_items.items_kits_id','=',$items_kits->id)
									  ->first();
			if(DB::table('item_kit_items')->where('id', '=', $get_data1->id)->delete()){
				$error = 0;
			}else{
				$error = 1;
			}
		}

		$result_chk=array_intersect($item_array,$item_array_old);
		print_r($result_chk);


		foreach ($result_chk as $key ) {
			$get_data = ItemKitItems::where('item_kit_items.item_id','=',$key)
									  ->where('item_kit_items.items_kits_id','=',$items_kits->id)
										->first();
			//print_r($get_data);
			//foreach ($get_data as $key) {
				$chk_item_id = $get_data->item_id;
				$chk_qty = $get_data->quantity;
			//}
			$clave = array_search($chk_item_id, $item_array);
			echo $item_array[$clave];
			echo $quantity_array[$clave];
			if($get_data->quantity!==$quantity_array[$clave]){
				$get_data->quantity = $quantity_array[$clave];
				if($get_data->save()){
					$error = 0;
				}else{
					$error = 1;
				}
			}else{
				$change = 0;
			}
		}
		//echo $error;
		if(isset($error)){
			if ($change == 0 | $error == 0){
				return Redirect::to('pos/items_kits/' . $items_kits->id . '/edit')->with('success', 'Se han guardado los cambios con éxito');
			}
		}
		else{
			return Redirect::to('pos/items_kits/' . $items_kits->id . '/edit')->with('success', 'No he ha modificado nada');
		}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  $items_kits
	 * @return Response
	 */
	public function getDelete($items_kits)
	{

		$item_kit_items = ItemKitItems::leftjoin('items','items.id','=','item_kit_items.item_id')
										->select(array('item_kit_items.id','items.name','item_kit_items.quantity','item_kit_items.item_id'))
										->where('item_kit_items.items_kits_id','=',$items_kits->id)->get();
		$title = "Kit de Artículos";
		$mode = "edit";
		// Title
        $title = 'Borrar Kit';

        // Show the page
        return View::make('pos/items_kits/delete', compact('items_kits', 'title', 'item_kit_items'));
	}

	public function postDelete($items_kits)
	{
		if(DB::table('item_kit_items')->where('items_kits_id', '=', $items_kits->id)->delete()){
			DB::table('items_kits')->where('id','=',$items_kits->id)->delete();
		}
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
