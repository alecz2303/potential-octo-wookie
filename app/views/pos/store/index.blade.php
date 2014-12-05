@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

@section('content')

	<h2>
		{{{ $title }}}
	</h2>
	<hr>

	<table id="store_table" class="cell-border display compact responsive dataTable">
		<thead>
			<th>Fecha</th>
			<th>Nombre</th>
			<th>Correo</th>
			<th># de Arts.</th>
			<th>Total</th>
			<th>Comentarios</th>
			<th>Acciones</th>
		</thead>
		<tfoot>
			<th>Fecha</th>
			<th>Nombre</th>
			<th>Correo</th>
			<th># de Arts.</th>
			<th>Total</th>
			<th>Comentarios</th>
			<th>Acciones</th>
		</tfoot>
	</table>
@stop

@section('scripts')
	<script type="text/javascript">
		var table;
		$(document).ready(function() {


			// Setup - add a text input to each footer cell
		    $('#store_table tfoot th').each( function () {
		        var title = $('#store_table thead th').eq( $(this).index() ).text();
		        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
		    } );

			table = $('#store_table').DataTable({
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
