@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
	<div class="page-header">
		<h2>
			{{{ $title }}}

			<div class="pull-right">
				<a href="{{{ URL::to('pos/items/create') }}}" class="button small iframe"><span class="fa fa-plus"></span> Crear</a>
			</div>
		</h2>
	</div>

	<table id="items" class="cell-border display compact responsive" width="100%">
		<thead>
			<tr>
				<th >UPC/EAN/ISBN</th>
				<th >Nombre del Artículo</th>
				<th >Categoría</th>
				<th >Precio de Compra</th>
				<th >Precio de Venta</th>
				<th >Cantidad en Stock</th>
				<th >Porcentaje de Impuesto(s)</th>
				<th style="white-space: nowrap">Inventario</th>
				<th >Acciones</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th >UPC/EAN/ISBN</th>
				<th >Nombre del Artículo</th>
				<th >Categoría</th>
				<th >Precio de Compra</th>
				<th >Precio de Venta</th>
				<th >Cantidad en Stock</th>
				<th >Porcentaje de Impuesto(s)</th>
				<th style="white-space: nowrap">Inventario</th>
				<th ></th>
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
		    $('#items tfoot th').each( function () {
		        var title = $('#items thead th').eq( $(this).index() ).text();
		        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
		    } );

			table = $('#items').DataTable({
		        "sAjaxSource": "{{ URL::to('pos/items/data') }}",
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
