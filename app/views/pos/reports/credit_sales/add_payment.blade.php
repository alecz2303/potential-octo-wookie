@extends('layouts.modal')
@section('content')
	{{number_format($dif,2)}}
	{{$sales->id}}
	{{Form::open()}}
		<div class="row">
			<div class="small-6 columns">
				<label>Debe:</label>
			</div>
			<div class="small-6 columns" align="right">
				<b><label id="totalDebe"></label></b>
			</div>
		</div>
		<div class="row">
			<div class="small-4 columns">
				Tipo de Pago:
			</div>
			<div class="small-8 columns">
				{{Form::select('payment_type', array('Efectivo' => 'Efectivo', 'Cheque' => 'Cheque', 'Gift Card' => 'Gift Card', 'Tarjeta de Débito' => 'Tarjeta de Débito', 'Tarjeta de Crédito' => 'Tarjeta de Crédito'),0,array('id'=>'payment_type'))}}
			</div>
		</div>
		<div class="row">
			<div class="small-4 columns">
				Cantidad Recibida:
			</div>
			<div class="small-8 columns">
				<label>
					{{Form::text('pay_qty',0,array('id'=>'pay_qty','required','pattern'=>'number'))}}
				</label>
			</div>
			<div class="row">
			<!-- Form Actions -->
				<div class="header panel clearfix" style="text-align:center !important">
					<a id="add_pay" class="button primary" >Agregar Pago</a>
				</div>
			<!-- ./ form actions -->
			</div>
			<div class="row">
			<!-- Form Actions -->
				<div class="header panel clearfix" style="text-align:center !important">
					<a href="{{{ URL::to('pos/sales') }}}" class="button tiny alert">Cancelar</a>
					<a id="submit" class="button success tiny" >Terminar</a>
				</div>
			<!-- ./ form actions -->
			</div>
			<div class="row">
				<table id="payments" class="dataTable">
					<thead>
						<tr>
							<th>Borrar</th>
							<th>Tipo</th>
							<th>Cantidad</th>
						</tr>
					</thead>
					<tbody id="paymentsBody">
					</tbody>
				</table>
			</div>
		</div>
	{{Form::close()}}
@stop
