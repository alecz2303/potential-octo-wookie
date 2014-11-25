@extends('layouts.default')
@section('content')
<div class="row">
<div class="large-12 columns">
	<div class="panel" align="center">
		<h1>Reporte de Resumen de Ventas</h1>
		<h4>{{$date_range}}</h4>
	</div>
</div>
</div>
<hr>
<table id="sales" class="responsive dataTable">
	<thead>
		<tr>
			<th >Venta</th>
			<th >Fecha</th>
			<th >Vendido a</th>
			<th >Subtotal</th>
			<th >Impuesto</th>
			<th >Total</th>
			<th >Diferencia</th>
			<th >Acciones</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($sales as $key => $value)
			@if($value->dif != 0)
			<tr>
				<td><a href='{{{ URL::to("pos/sales/$value->sale_id/receipt") }}}' target="_blank">{{$value->sale_id}}</a></td>
				<td>{{$value->created_at}}</td>
				<td>{{$value->full_name}}</td>
				<td>{{number_format($value->subtotal,2)}}</td>
				<td>{{number_format($value->tax,2)}}</td>
				<td>{{number_format($value->total,2)}}</td>
				<td>{{number_format($value->dif,2)}}</td>
				<td><a href='{{{ URL::to("pos/reports/credit_sales/$value->sale_id/$value->dif/add") }}}' class="iframe2 button tiny">Agregar Pago</a></td>
			</tr>
			@endif
		@endforeach
	</tbody>
</table>
<hr>
@stop

@section('scripts')
<!-- DataTables CSS -->
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">

		<!-- DataTables -->
		<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

		<script src="{{asset('js/jquery.colorbox.js')}}"></script>

	<script type="text/javascript">
		var table;
		$(document).ready(function() {
			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
			$(".iframe1").colorbox({iframe:true, width:"70%", height:"90%"});
			$(".iframe2").colorbox({iframe:true, width:"40%", height:"80%"});

		});
	</script>
@stop
