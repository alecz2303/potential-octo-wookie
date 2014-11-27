@extends('layouts.default')
@section('content')
<div class="row">
<div class="large-12 columns">
	<div class="panel" align="center">
		<h1>Abonos a Cuenta</h1>
		<h2>
			{{$customer_name->full_name}}
		</h2>
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
	</tbody>
</table>
<hr>

@stop

@section('scripts')
	<script type="text/javascript">
		var table;
		$(document).ready(function() {
			// Setup - add a text input to each footer cell
			$('#sales tfoot th').each( function () {
				var title = $('#items thead th').eq( $(this).index() ).text();
				$(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
			} );

			table = $('#sales').DataTable({
				searching: false,
				"ajax": {
					"url": "{{ URL::to('pos/payments/data') }}",
					"data": function ( d ) {
		                d.customer_id = {{$customer_id}};
		            }
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
