@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
	<div class="page-header">
		<h3>
			{{{ $title }}}

			<div class="pull-right">
				<a href="{{{ URL::to('pos/customers/create') }}}" class="button small iframe"><span class="fa fa-plus"></span> Crear</a>
			</div>
		</h3>
	</div>

	<table id="customers" class="cell-border display compact responsive" width="100%">
		<thead>
			<tr>
				<th class="col-md-2">Apellidos</th>
				<th class="col-md-2">Nombre</th>
				<th class="col-md-2">E-Mail</th>
				<th class="col-md-2">Teléfono</th>
				<th class="col-md-2">Acciones</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="col-md-2">Apellidos</th>
				<th class="col-md-2">Nombre</th>
				<th class="col-md-2">E-Mail</th>
				<th class="col-md-2">Teléfono</th>
				<th class="col-md-2"></th>
			</tr>
		</tfoot>
		<tbody>
		</tbody>
	</table>
@stop

@section('scripts')
	<script type="text/javascript">
		var table;
		$(document).ready(function() {


			// Setup - add a text input to each footer cell
		    $('#customers tfoot th').each( function () {
		        var title = $('#customers thead th').eq( $(this).index() ).text();
		        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
		    } );

			table = $('#customers').DataTable({
				"sAjaxSource": "{{ URL::to('pos/customers/data') }}",
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
