@extends('layouts.store')

@section('title')
	{{{ $title }}} :: @parent
@stop
@section('styles')
<style>
.ui-autocomplete-loading {
	background: white url('css/images/loading.gif') right center no-repeat;
}
#left{
}
#right{
	background-color: #ddd;
}
.total{
	color:black !important;
	background-color: black !important;
	height: 2px !important;
}
.hidden{
	display:none;
}
.no-close .ui-dialog-titlebar-close {
	display: none;
}
</style>
@stop
@section('content')
	@foreach ($company as $key => $value)
		@if($value->key == 'tax')
			<?php $tax = $value->value; ?>
		@endif
		@if($value->key == 'email')
			<?php $email_company = $value->value; ?>
		@endif
		@if($value->key == 'company')
			<?php $name_company = $value->value; ?>
		@endif
	@endforeach
	<h2>Pedidos en linea</h2>
	<hr>
	{{ Form::open(array('data-abide','autocomplete'=>'off','id'=>'store')) }}
		{{ Form::hidden('email-company', $email_company) }}
		{{ Form::hidden('name-company', $name_company) }}
		<div class="row">
			<div class="small-4 columns">
				<label>Nombre:
					{{ Form::text('nombre', null, array('required', 'pattern'=>'[a-zA-Z]+')) }}
				</label>
				<small class="error">El nombre es un campo requerido y solo admite texto</small>
			</div>
			<div class="small-4 columns">
				<label>Apellido Paterno
					{{ Form::text('ap_pat', null, array('required', 'pattern'=>'[a-zA-Z]+')) }}
				</label>
				<small class="error">El apellido paterno es un campo requerido y solo admite texto</small>
			</div>
			<div class="small-4 columns">
				<label>Apellido Materno
					{{ Form::text('ap_mat', null, array('pattern'=>'[a-zA-Z]+')) }}
				</label>
				<small class="error">El apellido materno solo admite texto</small>
			</div>
		</div>
		<div class="row">
			<div class="small-6 columns">
				<label>Correo Electrónico:
					{{ Form::email('email', null, array('required')) }}
				</label>
				<small class="error">Escriba un Correo Electrónico válido</small>
			</div>
			<div class="small-6 columns">
				<label>Teléfono:
					{{ Form::number('phone', null, array('required')) }}
				</label>
				<small class="error">Escriba un Correo Electrónico válido</small>
			</div>
		</div>
		<div class="row">
			<div class="small-12 columns">
				<label>Comentarios:
					{{ Form::textarea('comment',null,array('rows'=>'3')) }}
				</label>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="small-3 columns">
				<label for="right-label" class="right">Encontrar/Escanear Artículo:</label>
			</div>
			<div class="small-5 columns">
				{{ Form::text('item_name', null, array('id'=>'item_name'))}}
			</div>
			<div class="small-4 columns">

			</div>
		</div>
		<div class="small-9 columns" id='left'>
			<div class="row">
				<table id="store-table" class="cell-border display compact responsive dataTable" width="100%">
					<thead>
						<tr>
							<th >Borrar</th>
							<th >Nombre Art.</th>
							<th >Precio</th>
							<th >Cant.</th>
							<th >Total</th>
							<th >Borrar</th>
						</tr>
					</thead>

					<tbody id="storeBody">
					</tbody>
				</table>
			</div>
		</div>
		<div class="large-3 columns" id='right'>
			<div class="row">
				<div class="small-6 columns">
					<label>Sub-Total:</label>
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="subtotalVenta"></label></b>
				</div>
			</div>
			<div class="row">
				<div class="small-6 columns">
					<label>IVA %{{$tax}}:</label>
					{{Form::hidden('tax',$tax)}}
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="ivaVenta"></label></b>
				</div>
			</div>
			<div class="row">
				<div class="small-6 columns">
					<label>Total:</label>
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="totalVenta"></label></b>
				</div>
			</div>
			<hr class="total">
				<div class="row">
				<!-- Form Actions -->
					<div class="header panel clearfix" style="text-align:center !important">
						<a href="{{{ URL::to('/store') }}}" class="button tiny alert">Cancelar</a>
						<a id="submit" class="button success tiny" >Terminar</a>
					</div>
				<!-- ./ form actions -->
				</div>
			</div>
		</div>
	</div>
	{{ Form::close() }}
@stop

@section('scripts')
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.css') }}">
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="{{asset('js/jquery.number.min.js')}}"></script>

	<script type="text/javascript">
		var table;
		var selected_item = [];
		var counter = 0;
		var source;
		var data;
		var totPayment = 0;
		var giftCard = 0;
		var giftCardNumber = 0;
		var giftCardValue = 0;
		var control = 0;

		$(function()
		{
			counter = 0;
			$( "#item_name" ).autocomplete({
			minLength: 0,
			source:"store/auto",
			select: function(event, ui) {
				if(ui.item.tipo == 'Kit'){
					data = "term="+ui.item.id;
					$.ajax({
						type: "GET",
						dataType: "json",
						url: "store/autocompletekit", //Relative or absolute path to response.php file
						data: data,
						success: function(data) {
							data.forEach(function(entry) {
								if(jQuery.inArray( entry.id, selected_item ) < 0){
									console.log(entry);
									var table = document.getElementById("storeBody");
									var row_d = table.insertRow(0);
									var celda1 = row_d.insertCell(0);
									var celda2 = row_d.insertCell(1);
									celda1.colSpan = 3;
									celda1.innerHTML = "<small>Descripción:</small> "+entry.description;
									if(entry.is_serialized == 1){
										celda2.innerHTML = '<small>Número de serie:</small><input type="text" id="serialnumber_'+counter+'" name="entry['+counter+'][serialnumber]" />';
										celda2.colSpan = 3;
									}
									var row = table.insertRow(0);
									var cell1 = row.insertCell(0);
									var cell2 = row.insertCell(1);
									var cell3 = row.insertCell(2);
									var cell4 = row.insertCell(3);
									var cell5 = row.insertCell(4);
									var cell6 = row.insertCell(5);
									cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
									cell2.innerHTML = entry.name + '<input type="hidden" value="'+entry.id+'" name="entry['+counter+'][item]"/>' ;
									cell3.innerHTML = entry.cost;
									cell4.innerHTML = '<input type="text" value="'+entry.kitqty+'" id="qty_'+entry.id+'" name="entry['+counter+'][quantity]" onchange="total('+entry.id+','+entry.cost+')" />';
									cell5.innerHTML =  (entry.cost) * entry.kitqty ;
									cell5.id = entry.id;
									cell6.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
									$('#item_name').val('');
									counter += 1;

									selected_item.push(entry.id)
									finishTable();
									return false;
								}else{
									var cantidad = document.getElementById('qty_'+entry.id).value;
									cantidad = parseInt(cantidad) + parseInt(entry.kitqty);
									document.getElementById('qty_'+entry.id).value = cantidad;
									total(entry.id,entry.cost);
									finishTable();
									//alert('Ya se ha seleccionado este artículo.... '+entry.name+'...'+cantidad);
									$('#item_name').val('');
									return false
								}
							});
						}
					});
				}else{
					var data = "term="+ui.item.id;
					$.ajax({
						type: "GET",
						dataType: "json",
						url: "store/autocompleteitem", //Relative or absolute path to response.php file
						data: data,
						success: function(data) {
							data.forEach(function(entry) {
								if(jQuery.inArray( entry.id, selected_item ) < 0){
									var table = document.getElementById("storeBody");
									var row_d = table.insertRow(0);
									var celda1 = row_d.insertCell(0);
									var celda2 = row_d.insertCell(1);
									celda1.colSpan = 3;
									celda1.innerHTML = "<small>Descripción:</small> "+entry.description;
									if(entry.is_serialized == 1){
										celda2.innerHTML = '<small>Número de serie:</small><input type="text" id="serialnumber_'+counter+'" name="entry['+counter+'][serialnumber]" />';
										celda2.colSpan = 3;
									}
									var row = table.insertRow(0);
									if(counter%2!==0){
										row.id = 'alt';
									}
									var cell1 = row.insertCell(0);
									var cell2 = row.insertCell(1);
									var cell3 = row.insertCell(2);
									var cell4 = row.insertCell(3);
									var cell5 = row.insertCell(4);
									var cell6 = row.insertCell(5);
									cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
									cell2.innerHTML = entry.name + '<input type="hidden" value="'+entry.id+'" name="entry['+counter+'][item]"/>' ;
									cell3.innerHTML = entry.cost;
									cell4.innerHTML = '<input type="text" value="1" id="qty_'+entry.id+'" name="entry['+counter+'][quantity]" onchange="total('+entry.id+','+entry.cost+')" />';
									cell5.innerHTML =  (entry.cost) * 1 ;
									cell5.id = entry.id;
									cell6.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
									$('#item_name').val('');
									counter += 1;
									selected_item.push(entry.id)
									finishTable();
									return false;
								}else{
									var cantidad = document.getElementById('qty_'+entry.id).value;
									cantidad = parseInt(cantidad) + 1;
									document.getElementById('qty_'+entry.id).value = cantidad;
									total(entry.id,entry.cost);
									finishTable();
									//alert('Ya se ha seleccionado este artículo.... '+entry.name+'...'+cantidad);
									$('#item_name').val('');
									return false
								}
							});
						}
					});
				}
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a><small>"+ item.tipo + ':</small> ' + item.name + "<br>" + item.description + "<br>" + item.item_number + "</a>" )
				.appendTo( ul );
			};

		});

		function total(x,y){
			var w = 0;
			var z = document.getElementById("qty_"+x).value;
			var porcentaje = ((z * y)*w)/100
			var total = (z*y) - porcentaje
			document.getElementById(x).innerHTML = total.toFixed(2);
			finishTable();
		}

	function deleteRow(r,id) {
		var i = r.parentNode.parentNode.rowIndex;
		id = id.toString();
		var index = selected_item.indexOf(id);
		if(index > -1){
			selected_item.splice(index,1);
		}
		document.getElementById("store-table").deleteRow(i);
		document.getElementById("store-table").deleteRow(i);
		finishTable();
	} 



	var debugScript = false;

	function computeTableColumnTotal(tableId, colNumber)
	{
	// find the table with id attribute tableId
	// return the total of the numerical elements in column colNumber
	// skip the top row (headers) and bottom row (where the total will go)

	var result = 0;

	try
	{
		var tableElem = window.document.getElementById(tableId);
		var tableBody = tableElem.getElementsByTagName("tbody").item(0);
		var i;
		var howManyRows = tableBody.rows.length;
		for (i=0; i<(howManyRows); i = i+2) // skip first and last row (hence i=1, and howManyRows-1)
		{
		var thisTrElem = tableBody.rows[i];
		var thisTdElem = thisTrElem.cells[colNumber];
		var thisTextNode = thisTdElem.childNodes.item(0);
		if (debugScript)
		{
			window.alert("text is " + thisTextNode.data);
		} // end if

		// try to convert text to numeric
		var thisNumber = parseFloat(thisTextNode.data);
		// if you didn't get back the value NaN (i.e. not a number), add into result
		if (!isNaN(thisNumber))
			result += thisNumber;
		} // end for

	} // end try
	catch (ex)
	{
		window.alert("Exception in function computeTableColumnTotal()\n" + ex);
		result = 0;
	}
	finally
	{
		return result;
	}

	}

	var totalVenta = 0;
	var _total = 0;
	function finishTable()
	{
	if (debugScript)
		window.alert("Beginning of function finishTable");

	var tableElemName = "store";

	totalVenta = computeTableColumnTotal("store",4);
	//var totalPago = computeTableColumnTotal("payments",2);

	if (debugScript)
	{
		window.alert("totalVenta=" + totalVenta + "\n");
	}

		try
		{
			var subtotalVentaElem = window.document.getElementById("subtotalVenta");
			var ivaVentaElem = window.document.getElementById("ivaVenta");
			var totalVentaElem = window.document.getElementById("totalVenta");
			var tax = {{$tax}};
			tax = parseFloat(tax) / 100;

			var subTotal = totalVenta;
			var ivaVenta = (totalVenta * tax);
			_total = subTotal + ivaVenta;

			subtotalVentaElem.innerHTML =  $.number(subTotal,2);
			ivaVentaElem.innerHTML =  $.number(ivaVenta,2);
			totalVentaElem.innerHTML =  $.number(_total,2);

		}
		catch (ex)
		{
			window.alert("Exception in function finishTable()\n" + ex);
		}

	return;
	}

	var totalPagado = document.getElementById("totalPagado");
	var totalDebe = document.getElementById("totalDebe");
		$(function() {
			var form = document.getElementById("store");
			var counter = 0;
			var originalContent = "";
			var originalContentConfirm = "";
			
			$("#submit").click(function(){
				var totalVenta = computeTableColumnTotal("store-table",4);
				var totalVenta = _total;
				var pay_qty = totPayment;
				var dif;
				tipo = "Venta";
				form.submit();
			});
		});



	</script>
@stop