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
<table id="sales" class="responsive dataTable">
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
<!-- DataTables CSS -->
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/foundation/dataTables.foundation.css">
		<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.tablesTools.css')}}">

		<!-- DataTables -->

		<script src="{{asset('js/jquery.colorbox.js')}}"></script>
		<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>
		<script src="{{asset('js/dataTables.tableTools.js')}}"></script>

	<script type="text/javascript">
		var table;
		$(document).ready(function() {
			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
			$(".iframe1").colorbox({iframe:true, width:"70%", height:"90%"});
			$(".iframe2").colorbox({iframe:true, width:"40%", height:"80%"});

    		$('#sales').DataTable( {
				searching: false,
				"oLanguage": {
					"sLengthMenu": "Mostrar _MENU_ registros por página",
					"sInfo": "Mostrando _START_ de _END_ de _TOTAL_ registros",
				},
		        dom: 'T<"clear">lfrtip',
				tableTools: {
		            "sSwfPath": "../swf/copy_csv_xls_pdf.swf",
					"aButtons": [
		                {
		                    "sExtends": "copy",
		                    "sButtonText": "Copiar al portapapeles"
		                },
		                {
		                    "sExtends": "print",
		                    "sButtonText": "Imprimir"
		                },
		                {
		                    "sExtends":    "collection",
		                    "sButtonText": "Guardar",
		                    "aButtons":    [ "csv", "xls", "pdf" ]
		                }
		            ]
		        }
		    } );
		});
	</script>
@stop