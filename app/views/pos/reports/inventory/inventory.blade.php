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

	<table id="items" class="cell-border display compact responsive" width="100%">
		<thead>
			<tr>
				<th >Nombre del Artículo</th>
				<th >UPC/EAN/ISBN</th>
				<th >Descripción</th>
				<th >Cuenta</th>
				<th >Cuenta Mínima</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th >Nombre del Artículo</th>
				<th >UPC/EAN/ISBN</th>
				<th >Descripción</th>
				<th >Cuenta</th>
				<th >Cuenta Mínima</th>
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


	<script type="text/javascript">
		var table;
		$(document).ready(function() {


			// Setup - add a text input to each footer cell
			$('#items tfoot th').each( function () {
				var title = $('#items thead th').eq( $(this).index() ).text();
				$(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
			} );

			table = $('#items').DataTable({
				"sAjaxSource": "{{ URL::to('pos/reports/datainventory') }}",
				tableTools: {
	            	"sSwfPath": "{{URL::asset('swf/copy_csv_xls_pdf.swf')}}"
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
