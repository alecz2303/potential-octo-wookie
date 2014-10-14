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
				<a href="{{{ URL::to('pos/items/create') }}}" class="button small iframe"><span class="fa fa-plus"></span> Crear</a>
			</div>
		</h3>
	</div>

	<table id="items" class="responsive">
		<thead>
			<tr>
				<th class="col-md-2">UPC/EAN/ISBN</th>
				<th class="col-md-2">Nombre del Artículo</th>
				<th class="col-md-2">Categoría</th>
				<th class="col-md-2">Precio de Compra</th>
				<th class="col-md-2">Precio de Venta</th>
				<th class="col-md-2">Cantidad en Stock</th>
				<th class="col-md-2">Porcentaje de Impuesto(s)</th>
				<th class="col-md-2">Inventario</th>
				<th class="col-md-2">Acciones</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="col-md-2">UPC/EAN/ISBN</th>
				<th class="col-md-2">Nombre del Artículo</th>
				<th class="col-md-2">Categoría</th>
				<th class="col-md-2">Precio de Compra</th>
				<th class="col-md-2">Precio de Venta</th>
				<th class="col-md-2">Cantidad en Stock</th>
				<th class="col-md-2">Porcentaje de Impuesto(s)</th>
				<th class="col-md-2">Inventario</th>
				<th class="col-md-2"></th>
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
				responsive: true,
				"oLanguage": {
					"sLengthMenu": "_MENU_ registros por página"
				},
				"bProcessing": true,
		        "bServerSide": true,
		        "sAjaxSource": "{{ URL::to('pos/items/data') }}",
		        "fnDrawCallback": function ( oSettings ) {
	           		$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
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