@extends('layouts.default')
@section('content')
<div class="row">
<div class="large-12 columns">
	<div class="panel" align="center">
		<h1>Abonos a Cuenta</h1>
		<h4>{{$sales['0']['full_name']}}</h4>
	</div>
</div>
</div>
<hr>
<table id="sales" class="cell-border display compact responsive" width="100%">
	<thead>
		<tr>
			<th >Venta</th>
			<th >Fecha</th>
			<th >Total</th>
			<th >Deuda</th>
			<th >Acciones</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($sales as $key => $value)
			@if($value->dif != 0)
			<tr>
				<td><a href='{{{ URL::to("pos/sales/$value->sale_id/receipt") }}}' target="_blank">{{$value->sale_id}}</a></td>
				<td>{{$value->created_at}}</td>
				<td>$ {{number_format($value->total,2)}}</td>
				<td>$ {{number_format($value->dif,2)}}</td>
				<td><a href='{{{ URL::to("pos/payments/$value->sale_id/$value->dif/$value->customer_id/add") }}}' class="iframe1 button tiny">Agregar Pago</a></td>
			</tr>
			@endif
		@endforeach
		@foreach ($sales_no_pay as $key => $value)
			@if($value->dif != 0)
			<tr>
				<td><a href='{{{ URL::to("pos/sales/$value->sale_id/receipt") }}}' target="_blank">{{$value->sale_id}}</a></td>
				<td>{{$value->created_at}}</td>
				<td>$ {{number_format($value->total,2)}}</td>
				<td>$ {{number_format($value->dif,2)}}</td>
				<td><a href='{{{ URL::to("pos/payments/$value->sale_id/$value->dif/$value->customer_id/add") }}}' class="iframe1 button tiny">Agregar Pago</a></td>
			</tr>
			@endif
		@endforeach
	</tbody>
</table>
<hr>
<?php
	$total_adeudo = 0;
	foreach ($sales as $key => $value){
		if($value->dif != 0){
			$total_adeudo += $value->dif;
		}
	}
	foreach ($sales_no_pay as $key => $value){
		if($value->dif != 0){
			$total_adeudo += $value->dif;
		}
	}
?>
<ul class="pricing-table">
	<li class="title">Total Adeudo <b>{{$sales['0']['full_name']}}</b></li>
	<li class="price">$ {{number_format($total_adeudo,2)}}</li>
</ul>

@stop

@section('scripts')
	<script type="text/javascript">
		var table;
		$(document).ready(function() {
    		table = $('#sales').DataTable( {
				searching: false,
				"ajax": ''
		    } );
		});
	</script>
@stop
