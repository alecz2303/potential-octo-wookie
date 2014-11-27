<?php

class PaymentsController extends PosDashboardController {

	/**
	* SalesPayments Model
	* @var sales_payments
	* @var sales
	*/
	protected $sales_payments;
	protected $sales;
	/**
	* Inject the models.
	* @param SalesPayments $sales_payments
	* @param Sales $sales
	*/
	public function __construct(SalesPayments $sales_payments, Sales $sales)
	{
		parent::__construct();
		$this->sales_payments = $sales_payments;
		$this->sales = $sales;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = "Abono a Cuenta";
		$customer_options = DB::table('customers')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('customers.id,CONCAT(peoples.first_name," ",peoples.last_name) as full_name')
							->where('deleted','=',0)
							->orderBy('peoples.last_name', 'asc')
							->orderBy('peoples.first_name', 'asc')
							->lists('full_name','id');
		return View::make('pos/payments/index',compact('title','customer_options'));
	}

	public function postIndex()
	{
		$customer_id = Input::get('customer_id');
		$sales_tot = DB::table('credit_sales')
											->select('sale_id','created_at','total','dif')
											->where('customer_id','=',$customer_id)
											->where('dif','>',0)
							->get();
		$customer_name = Peoples::rightjoin('customers','customers.people_id','=','peoples.id')
								->selectRaw('CONCAT(peoples.first_name," ",last_name) as full_name')
								->where('customers.id','=',$customer_id)
								->where('customers.deleted','=',0)
								->first();



		return View::make('pos/payments/report', compact('sales_tot','customer_id','customer_name'));
	}

	public function getData()
	{
		$customer_id = Input::get('customer_id');

		$sales_tot = DB::table('credit_sales')
						->select('sale_id','created_at','total','dif')
						->where('customer_id','=',$customer_id)
						->where('dif','>',0);

		return Datatables::of($sales_tot)
		->add_column('Acciones', '<ul class="stack button-group round">
										<li><a href="{{{ URL::to(\'pos/payments/\' . $sale_id . \'/\' . $dif . \'/\' . Input::get("customer_id") . \'/add\' ) }}}" class="iframe1 button tiny">Agregar Pago</a></li>
								</ul>
			')
		->edit_column('total','$ {{number_format($total,2)}}')
		->edit_column('dif','$ {{number_format($dif,2)}}')
		->edit_column('sale_id','
			<ul class="stack button-group round">
				<li><a href="{{{ URL::to(\'pos/sales/\' . $sale_id . \'/receipt\' ) }}}" target="_blank" class="button tiny"> {{$sale_id}} </a></li>
			</ul>
		')
		->remove_column('customer_id')
		->remove_column('full_name')
		->remove_column('payment_amount')
		->remove_column('subtotal')
		->remove_column('tax')
		->make();
	}

	public function getAdd_payment($sales,$dif,$customer_id)
	{
		$title = 'Agregar pago';
		$sales_payments = SalesPayments::where('sale_id','=',$sales->id)->get();
		return View::make('pos/payments/add_payment', compact('sales','dif','customer_id','title','sales_payments'));
	}

	public function getGiftcardsnumbers()
	{
		$term = Input::get('term');
		$results = array();
		$queries = DB::table('giftcards')
		->distinct()
		->leftjoin('peoples','giftcards.people_id','=','peoples.id')
		->select(array('giftcards.id','giftcards.number','giftcards.value','giftcards.deleted','peoples.first_name','peoples.last_name'))
		->where('number','=',$term)
		->get();
		foreach ($queries as $query)
		{
			$results[] = [
				'id' => $query->id,
				'number' => $query->number,
				'value' => $query->value,
				'deleted' => $query->deleted,
				'first_name' => $query->first_name,
				'last_name' => $query->last_name
			];
		}
		return Response::json($results);
	}

	public function postAdd_payment($sales,$dif,$customer_id)
	{
		echo "<pre>";
			print_r(Input::all());
		echo "</pre>";

		#################################
		##		Mensajes de Error      ##
		#################################
		$messages = array(
			'payment.required' => 'Debe tener al menos un pago',
		);

		#################################
		##		Datos a validar        ##
		#################################
		$data = array(
			'customer_id' => $customer_id,
			'user_id' => Auth::user()->id,
			'payment' => Input::get('payment'),
		);

		#################################
		##		Reglas de validación   ##
		#################################
		$rules = array(
			'payment' => 'required|array',
		);

		#################################
		##    Validación de los datos  ##
		#################################
		$validator = Validator::make($data,$rules,$messages);

		if($validator->fails()){
			$messages = $validator->messages();
			echo "<hr>";
			echo "<pre>";
			print_r($messages);
			echo "</pre>";
			return Redirect::to("pos/payments/$sales->id/$dif/$customer_id/add")
							->withErrors($messages)
							->withInput();
		}

		foreach (Input::get('payment') as $type => $value) {
			if($type == 'Efectivo' | $type == 'Tarjeta de Crédito' | $type == 'Tarjeta de Débito' | $type == 'Cheque'){
				$sales->payment_type .= $type.': '.$value.'<br/>';
				$dif = $dif-$value;
			}else{
				$sales->payment_type .= 'Gift Card: '. $type .': '.$value.'<br/>';
				$dif = $dif-$value;
				######################################################################
				$giftcards = Giftcards::where('number','=',$type)->first();
				$giftcards->value = $giftcards->value -$value;
				$giftcards->save();
			}
		}
		echo $sales->payment_type;
		if($sales->save()){
			if(Input::get('payment')){
				foreach (Input::get('payment') as $type => $value) {
					$this->sales_payments = new SalesPayments;
					$this->sales_payments->sale_id = $sales->id;
					if($type == 'Efectivo' | $type == 'Tarjeta de Crédito' | $type == 'Tarjeta de Débito' | $type == 'Cheque'){
						$this->sales_payments->payment_type = $type;
						$this->sales_payments->payment_amount = $value;
					}else{
						$this->sales_payments->payment_type = 'Gift Card: '.$type;
						$this->sales_payments->payment_amount = $value;
					}
					$this->sales_payments->save();
				}
			}
		}
		return Redirect::to("pos/payments/$sales->id/$dif/$customer_id/add")->with('success','Se han guardado el(los) pago(s) con éxito.');
	}
}
