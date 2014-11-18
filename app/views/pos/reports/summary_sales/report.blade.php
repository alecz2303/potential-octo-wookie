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
<table id="sales" class="responsive">
	<thead>
		<tr>
			<th >Fecha</th>
			<th >Subtotal</th>
			<th >Impuesto</th>
			<th >Total</th>
			<th >Ganancia</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th >Fecha</th>
			<th >Subtotal</th>
			<th >Impuesto</th>
			<th >Total</th>
			<th >Ganancia</th>
		</tr>
	</tfoot>
	<tbody>
	</tbody>
</table>
<hr>
<?php
	$subtotal = 0;
	$total = 0;
	$tax = 0;
	$ganancia = 0;
	foreach ($sales as $key => $value){
		$subtotal += $value->subtotal;
		$total += $value->total;
		$tax += $value->tax;
		$ganancia += $value->ganancia;
	}
?>
<ul class="pricing-table">
	<li class="title">Resumen Ventas</li>
	<li class="bullet-item">Sub Total: {{number_format($subtotal,2)}}</li>
	<li class="bullet-item">Total: {{number_format($total,2)}}</li>
	<li class="bullet-item">Impuesto: {{number_format($tax,2)}}</li>
	<li class="bullet-item">Ganancia: {{number_format($ganancia,2)}}</li>
</ul>
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


			// Setup - add a text input to each footer cell
			$('#sales tfoot th').each( function () {
				var title = $('#sales thead th').eq( $(this).index() ).text();
				$(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
			} );

			table = $('#sales').DataTable({
				"order": [ 0, 'asc' ],
				responsive: true,
				"oLanguage": {
					"sLengthMenu": "_MENU_ registros por página"
				},
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "{{ URL::to('pos/reports/datasummarysales?date_range='.$date_range.'&whereRaw='.$whereRaw) }}",
				"fnDrawCallback": function ( oSettings ) {
					$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
					$(".iframe1").colorbox({iframe:true, width:"70%", height:"90%"});
					$(".iframe2").colorbox({iframe:true, width:"40%", height:"50%"});
				}
			});

			// Apply the search
			table.columns().eq( 0 ).each( function ( colIdx ) {
				$( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
					table
						.column( colIdx )
						.search( this.value )
						.draw();
				} );
			} );
		});
	</script>
@stop
