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
				<a href="{{{ URL::to('pos/items_kits/create') }}}" class="button small iframe"><span class="fa fa-plus"></span> Crear</a>
			</div>
		</h3>
	</div>

	<table id="items" class="cell-border display compact responsive" width="100%">
		<thead>
			<tr>
				<th >Nombre del Kit de Artículos</th>
				<th >Descripción del Kit de Artículos</th>
				<th >Acciones</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th >Nombre del Kit de Artículos</th>
				<th >Descripción del Kit de Artículos</th>
				<th ></th>
			</tr>
		</tfoot>
		<tbody>
		</tbody>
	</table>
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
		    $('#items tfoot th').each( function () {
		        var title = $('#items thead th').eq( $(this).index() ).text();
		        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
		    } );

			table = $('#items').DataTable({
		        "sAjaxSource": "{{ URL::to('pos/items_kits/data') }}",
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
