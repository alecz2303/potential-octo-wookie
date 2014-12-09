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

	<table id="store_table" class="cell-border display compact responsive">
		<thead>
			<th>Fecha</th>
			<th>Nombre</th>
			<th>Correo</th>
			<th># de Arts.</th>
			<th>Total</th>
			<th>Comentarios</th>
			<th>Acciones</th>
		</thead>
	</table>
@stop

@section('scripts')
	<script src="{{ asset('js/dataTables.fixedColumns.js') }}"></script>
	<script type="text/javascript">
		var table;
		$(document).ready(function() {

			table = $('#store_table').DataTable({
				"responsive": true,
				"sAjaxSource": "{{ URL::to('pos/store/data') }}",
		        scrollX:        true,
		        scrollCollapse: true,
				columnDefs: [
		            { width: '20%', targets: 0 },
		            { width: '25%', targets: 1 },
		            { width: '20%', targets: 2 },
		            { width: '10%', targets: 3 },
		            { width: '10%', targets: 4 },
		            { width: '40%', targets: 5 },
		            { width: '15%', targets: 6 }
		        ]
			});

		});
	</script>
@stop
