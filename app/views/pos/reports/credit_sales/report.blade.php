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
			<th >Venta</th>
			<th >Fecha</th>
			<th >Artículos Comprados</th>
			<th >Vendido por</th>
			<th >Vendido a</th>
			<th >Subtotal</th>
			<th >Impuesto</th>
			<th >Total</th>
			<th >Ganancia</th>
			<th >Tipo de Pago</th>
			<th >Comentario</th>
			<th >Acciones</th>
		</tr>
	</thead>
	<tbody>
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


			table = $('#sales').DataTable({
				"order": [ 0, 'asc' ],
				responsive: true,
				searching: false,
				"oLanguage": {
					"sLengthMenu": "_MENU_ registros por página"
				},
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "{{ URL::to('pos/reports/datadetailsales?date_range='.$date_range.'&whereRaw='.$whereRaw) }}",
				"fnDrawCallback": function ( oSettings ) {
					$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
					$(".iframe1").colorbox({iframe:true, width:"70%", height:"90%"});
					$(".iframe2").colorbox({iframe:true, width:"40%", height:"80%"});
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
