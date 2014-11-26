@extends('layouts.default')
@section('content')
<div class="row">
<div class="large-12 columns">
	<div class="panel" align="center">
		<h1>Reporte de Resumen de Entradas</h1>
		<h4>{{$date_range}}</h4>
	</div>
</div>
</div>
<hr>
<table id="receivings" class="cell-border display compact responsive" width="100%">
	<thead>
		<tr>
			<th >Recepción</th>
			<th >Fecha</th>
			<th >Artículos Recibidos</th>
			<th >Recibido por</th>
			<th >Provisto por</th>
			<th >Total</th>
			<th >Tipo de Pago</th>
			<th >Comentario</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<hr>
	<?php
			$total = 0;
			foreach ($receivings as $key => $value){
					$total += $value->total;
			}
	?>
	<ul class="pricing-table">
			<li class="title">Resumen Entradas</li>
			<li class="bullet-item">Total: {{number_format($total,2)}}</li>
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


			table = $('#receivings').DataTable({
				searching: false,
				tableTools: {
					"sSwfPath": "{{URL::asset('swf/copy_csv_xls_pdf.swf')}}"
				},
				 "sAjaxSource": "{{ URL::to('pos/reports/datadetailreceivings?date_range='.$date_range.'&whereRaw='.$whereRaw) }}",
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
