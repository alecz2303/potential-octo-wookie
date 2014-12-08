@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop
@section('styles')
<style>
.ui-autocomplete-loading {
background: white url('../css/images/loading.gif') right center no-repeat;
}
.hidden{
	display:none;
}
.no-close .ui-dialog-titlebar-close {
	display: none;
}
</style>
@stop

{{-- Content --}}
@section('content')
	<h2>
		{{{ $title }}}
	</h2>
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off', 'id'=>'surtir')) }}
		<div class="row collapse">
			<label>Nombre del Cliente:</label>
			<div class="small-9 columns ui-widget">
				{{ Form::text('customer_name',null,array('id'=>'customer_name','class'=>'ui-autocomplete-loading')) }}
			</div>
			<div class="small-3 columns">
	        	<span class="postfix"><i class="fa fa-search"></i></span>
	        </div>
		</div>
        <div class="hidden" id="divHidden">
			<a href="#" class="button tiny alert" id="delCustomer" onclick="delCustomer()"><span class="fa fa-minus"></span> Quitar Cliente</a>
		</div>
			{{ Form::hidden('customer_id', null, array('id'=>'customer_id'))}}
			<a href="{{{ URL::to('pos/customers/create') }}}" class="button tiny iframe"><span class="fa fa-plus"></span> Nuevo Cliente</a>
		<table id="items" class="cell-border display compact responsive" width="100%">
			<CAPTION>
				Pedido solicitado por: <i>{{ $store_orders->nombre.' '.$store_orders->ap_pat.' '.$store_orders->ap_mat }}</i><br>
				Comentario: <i>{{ $store_orders->comment }}</i>
			</CAPTION>
			<thead>
				<tr>
					<th>Nombre Art.</th>
					<th>Cant. Solicitada</th>
					<th>Precio</th>
					<th>Impuesto</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($store_orders_items as $key => $value)
					<tr>
						<td>{{ $value->name }}</td>
						<td style="text-align:right;">{{ $value->quantity_purchased }}</td>
						<td style="text-align:right;">$ {{ number_format($value->item_unit_price,2) }}</td>
						<td style="text-align:right;">$ {{ number_format(($value->quantity_purchased * $value->item_unit_price)*($value->percent/100),2) }}</td>
						<td style="text-align:right;">$ {{ number_format(($value->quantity_purchased * $value->item_unit_price)+($value->quantity_purchased * $value->item_unit_price)*($value->percent/100),2) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<a class="button expand success" id="submit">Surtir pedido</a>
	{{ Form::close() }}
	<table>
		
	</table>

	<div id="dialog-confirm" title="">
	</div>
@stop

@section('scripts')
	<link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}">
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script type="text/javascript">

		function delCustomer(){
			$('#customer_name').val('');
			$('#customer_id').val('');
			document.getElementById('customer_name').readOnly = false;
			$('#divHidden').toggle();
		}

		$(document).ready(function() {
			$('#items').DataTable({
				"paging": false,
				"info": false,
				"dom":'<"clear">',
			});	
		});

		$( "#customer_name" ).autocomplete({
		source: "../../sales/customers",
		minLength: 0,
		select: function(event, ui) {
			$('#customer_name').val(ui.item.customer_name);
			$('#customer_id').val(ui.item.id);
			$('#divHidden').toggle();
			document.getElementById('customer_name').readOnly = true;
			return false;
		}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
			.append( "<a>" + item.customer_name + "</a>" )
			.appendTo( ul );
		};

		$(function(){
			var form = document.getElementById("surtir");
			var originalContent = "";
			$("#dialog-confirm").dialog({
				dialogClass: "no-close",
				autoOpen: false,
				resizable: false,
				modal: true,
				buttons: {
					"Procesar": function() {
						form.submit();
						$( this ).dialog( "close" );
					},
					Cancelar: function() {
						$( this ).dialog( "close" );
					}
				},
				close : function(event, ui) {
					$("#dialog-confirm").html(originalContent);
					console.log(originalContent);
				}
			});

			$("#submit").click(function(){
				title="¿Surtir pedido?",
				$( "#dialog-confirm" ).dialog( "option", "title", title );
				$( ".ui-dialog-content" ).append("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Una vez procesado, no podrá deshacerse.</p>");
				$( "#dialog-confirm" ).dialog( "open" );
			});
		});
	</script>
@stop
