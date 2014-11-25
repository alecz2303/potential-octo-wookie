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
							->lists('full_name','id');
		return View::make('pos/payments/index',compact('title','customer_options'));
	}

	public function postIndex()
	{
		$date_range = date("'1978-03-23'")." and ".date("'Y-m-d 23:59:59'");
		$customer_id = Input::get('customer_id');
		$sales = SalesPayments::leftjoin('sales','sales.id','=','sales_payments.sale_id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('sales_payments.sale_id,sales.customer_id,SUBSTRING(sales.created_at,1,10) as created_at,CONCAT(first_name," ",last_name) as full_name,SUM(payment_amount) as payment_amount,
							(SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) as subtotal,
							(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id) as tax,
							(SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) +
							(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id) as total,
							((SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) +
							(SELECT (sales_items_taxes.percent / 100) * (SELECT SUM(sales_items.quantity_purchased * item_unit_price) FROM sales_items WHERE sales_items.sale_id = sales_payments.sale_id) FROM sales_items_taxes WHERE sales_items_taxes.sale_id = sales_payments.sale_id)) - SUM(payment_amount) as dif')
							->whereRaw('sales_payments.sale_id NOT IN (SELECT id FROM sales WHERE customer_id = 0)')
							->whereRaw('sales.created_at between '.$date_range)
							->whereRaw('sales.customer_id = '.$customer_id)
							->groupBy('sales_payments.sale_id')
							->orderBy('sales.created_at')
							->get();
		$sales_no_pay = Sales::leftjoin('sales_items','sales_items.sale_id','=','sales.id')
							->leftjoin('sales_items_taxes','sales_items_taxes.sale_id','=','sales.id')
							->leftjoin('customers','customers.id','=','sales.customer_id')
							->leftjoin('peoples','peoples.id','=','customers.people_id')
							->selectRaw('sales.id as sale_id,sales.customer_id,SUBSTRING(sales.created_at,1,10) as created_at,CONCAT(first_name," ",last_name) as full_name,0 as payment_amount,
											sum(quantity_purchased * item_unit_price) as subtotal,
											percent/100 * sum(quantity_purchased * item_unit_price)  as tax,
											sum(quantity_purchased * item_unit_price) + (percent/100 * sum(quantity_purchased * item_unit_price)) as total,
											sum(quantity_purchased * item_unit_price) + (percent/100 * sum(quantity_purchased * item_unit_price)) as dif')
							->whereRaw('sales.id not in (select sale_id from sales_payments)')
							->whereRaw('sales.created_at between '.$date_range)
							->whereRaw('sales.customer_id = '.$customer_id)
							->groupBy('sales.id')
							->orderBy('sales.created_at')
							->get();

		return View::make('pos/payments/report', compact('sales','sales_no_pay','date_range','whereRaw'));
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
