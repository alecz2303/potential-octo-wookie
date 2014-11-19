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

		</h3>
	</div>

	<table id="items" class="responsive">
		<thead>
			<tr>
				<th >Nombre del Artículo</th>
				<th >UPC/EAN/ISBN</th>
				<th >Descripción</th>
				<th >Cuenta</th>
				<th >Cuenta Mínima</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
@stop

@section('scripts')
<!-- DataTables CSS -->
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">

		<!-- DataTables -->
		<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>


	<script type="text/javascript">
		var table;
		$(document).ready(function() {


			table = $('#items').DataTable({
				responsive: true,
				searching: false,
				"oLanguage": {
					"sLengthMenu": "_MENU_ registros por página"
				},
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "{{ URL::to('pos/reports/datalow') }}",
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
