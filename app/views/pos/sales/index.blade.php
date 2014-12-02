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
{{-- Content --}}
@section('content')

@foreach ($company as $key => $value)
	@if($value->key == 'tax')
		<?php $tax = $value->value ?>
	@endif
@endforeach


	<h2 align="center">
		{{{ $title }}}
	</h2>
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off', 'id'=>'entrega'))}}
	<div class="row">
		<div class="small-9 columns" id='left'>
			<div class="row">
				<div class="small-3 columns">
		            <label for="right-label" class="right">Modo:</label>
		        </div>
				<div class="small-5 columns">
		          	{{Form::select('tipo', array('0' => 'Venta', '1' => 'Devolución'),0,array('id'=>'right-label'))}}
		        </div>
				<div class="small-4 columns">
				</div>
			</div>
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
			<div class="row">
				<table id="sales" class="cell-border display compact responsive dataTable" width="100%">
					<thead>
						<tr>
							<th >Borrar</th>
							<th >Nombre Art.</th>
							<th >Inventario</th>
							<th >Precio</th>
							<th >Cant.</th>
							<th >%Desc.</th>
							<th >Total</th>
							<th >Borrar</th>
						</tr>
					</thead>

					<tbody id="receivingsBody">
					</tbody>
				</table>
			</div>
		</div>
		<div class="large-3 columns" id='right'>
			<div class="row">
				<label>
					Seleccionar Cliente
				</label>
				{{ Form::text('customer_name', null, array('id'=>'customer_name'))}}
				<div class="hidden" id="divHidden">
					<a href="#" class="button tiny alert" id="delCustomer" onclick="delCustomer()"><span class="fa fa-minus"></span> Quitar Cliente</a>
				</div>
				{{ Form::hidden('customer_id', null, array('id'=>'customer_id'))}}
				<a href="{{{ URL::to('pos/customers/create') }}}" class="button tiny iframe"><span class="fa fa-plus"></span> Nuevo Cliente</a>
			<hr class="total">
			</div>
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
				<div class="small-12 columns">
					<label>Comentarios:
						{{Form::textarea('comment',null,array('rows'=>'3'))}}
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-6 columns">
					<label>Pagado:</label>
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="totalPagado"></label></b>
				</div>
			</div>
			<div class="row">
				<div class="small-6 columns">
					<label>Debe:</label>
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="totalDebe"></label></b>
				</div>
			</div>
			<div class="row">
				<div class="small-4 columns">
					Tipo de Pago:
				</div>
				<div class="small-8 columns">
					{{Form::select('payment_type', array('Efectivo' => 'Efectivo', 'Cheque' => 'Cheque', 'Gift Card' => 'Gift Card', 'Tarjeta de Débito' => 'Tarjeta de Débito', 'Tarjeta de Crédito' => 'Tarjeta de Crédito'),0,array('id'=>'payment_type'))}}
				</div>
			</div>
			<div class="row">
				<div class="small-4 columns">
					Cantidad Recibida:
				</div>
				<div class="small-8 columns">
					<label>
						{{Form::text('pay_qty',0,array('id'=>'pay_qty','required','pattern'=>'number'))}}
					</label>
				</div>
				<div class="row">
				<!-- Form Actions -->
					<div class="header panel clearfix" style="text-align:center !important">
						<a id="add_pay" class="button primary" >Agregar Pago</a>
					</div>
				<!-- ./ form actions -->
				</div>
				<div class="row">
				<!-- Form Actions -->
					<div class="header panel clearfix" style="text-align:center !important">
						<a href="{{{ URL::to('pos/sales') }}}" class="button tiny alert">Cancelar</a>
						<a id="submit" class="button success tiny" >Terminar</a>
					</div>
				<!-- ./ form actions -->
				</div>
				<div class="row">
					<table id="payments" class="dataTable cell-border display compact responsive">
						<thead>
							<tr>
								<th>Borrar</th>
								<th>Tipo</th>
								<th>Cantidad</th>
								<th>Borrar</th>
							</tr>
						</thead>
						<tbody id="paymentsBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="dialog-confirm" title="">
	</div>

	<div id="dialog-badcard" title="">
	</div>
	{{ Form::close() }}
@stop

@section('scripts')
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
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

		$(document).ready(function() {

			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
			table = null;
			$('#payments').DataTable({
				paging: false,
				searching: false,
			});
		});

		$(function()
		{
			counter = 0;
			$( "#item_name" ).autocomplete({
			minLength: 0,
			source:"sales/auto",
			select: function(event, ui) {
				if(ui.item.tipo == 'Kit'){
					data = "term="+ui.item.id;
					$.ajax({
						type: "GET",
						dataType: "json",
						url: "sales/autocompletekit", //Relative or absolute path to response.php file
						data: data,
						success: function(data) {
							data.forEach(function(entry) {
								if(jQuery.inArray( entry.id, selected_item ) < 0){
									console.log(entry);
									var table = document.getElementById("receivingsBody");
									var row_d = table.insertRow(0);
									var celda1 = row_d.insertCell(0);
									var celda2 = row_d.insertCell(1);
									celda1.colSpan = 3;
									celda1.innerHTML = "<small>Descripción:</small> "+entry.description;
									if(entry.is_serialized == 1){
										celda2.innerHTML = '<small>Número de serie:</small><input type="text" id="serialnumber_'+counter+'" name="entry['+counter+'][serialnumber]" />';
										celda2.colSpan = 5;
									}
									var row = table.insertRow(0);
									var cell1 = row.insertCell(0);
									var cell2 = row.insertCell(1);
									var cell3 = row.insertCell(2);
									var cell4 = row.insertCell(3);
									var cell5 = row.insertCell(4);
									var cell6 = row.insertCell(5);
									var cell7 = row.insertCell(6);
									var cell8 = row.insertCell(7);
									cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
									cell2.innerHTML = entry.name + '<input type="hidden" value="'+entry.id+'" name="entry['+counter+'][item]"/>' ;
									cell3.innerHTML = entry.qty;
									cell4.innerHTML = entry.cost;
									cell5.innerHTML = '<input type="text" value="'+entry.kitqty+'" id="qty_'+entry.id+'" name="entry['+counter+'][quantity]" onchange="total('+entry.id+','+entry.cost+')" />';
									cell6.innerHTML = '<input type="text" value="0" id="desc_'+entry.id+'" name="entry['+counter+'][desc]" onchange="total('+entry.id+','+entry.cost+')" />';
									cell7.innerHTML =  (entry.cost) * entry.kitqty ;
									cell7.id = entry.id;
									cell8.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
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
						url: "sales/autocompleteitem", //Relative or absolute path to response.php file
						data: data,
						success: function(data) {
							data.forEach(function(entry) {
								if(jQuery.inArray( entry.id, selected_item ) < 0){
									var table = document.getElementById("receivingsBody");
									var row_d = table.insertRow(0);
									var celda1 = row_d.insertCell(0);
									var celda2 = row_d.insertCell(1);
									celda1.colSpan = 3;
									celda1.innerHTML = "<small>Descripción:</small> "+entry.description;
									if(entry.is_serialized == 1){
										celda2.innerHTML = '<small>Número de serie:</small><input type="text" id="serialnumber_'+counter+'" name="entry['+counter+'][serialnumber]" />';
										celda2.colSpan = 5;
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
									var cell7 = row.insertCell(6);
									var cell8 = row.insertCell(7);
									cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
									cell2.innerHTML = entry.name + '<input type="hidden" value="'+entry.id+'" name="entry['+counter+'][item]"/>' ;
									cell3.innerHTML = entry.qty;
									cell4.innerHTML = entry.cost;
									cell5.innerHTML = '<input type="text" value="1" id="qty_'+entry.id+'" name="entry['+counter+'][quantity]" onchange="total('+entry.id+','+entry.cost+')" />';
									cell6.innerHTML =  '<input type="text" value="0" id="desc_'+entry.id+'" name="entry['+counter+'][desc]" onchange="total('+entry.id+','+entry.cost+')" />';
									cell7.innerHTML =  (entry.cost) * 1 ;
									cell7.id = entry.id;
									cell8.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
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

			$( "#customer_name" ).autocomplete({
			source: "sales/customers",
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
		});

		function total(x,y){
			var w = document.getElementById("desc_"+x).value;
			var z = document.getElementById("qty_"+x).value;
			var porcentaje = ((z * y)*w)/100
			var total = (z*y) - porcentaje
			document.getElementById(x).innerHTML = total.toFixed(2);
			finishTable();
		}

		function delCustomer(){
			$('#customer_name').val('');
			$('#customer_id').val('');
			document.getElementById('customer_name').readOnly = false;
			$('#divHidden').toggle();
		}

	function deleteRow(r,id) {
		var i = r.parentNode.parentNode.rowIndex;
		id = id.toString();
		var index = selected_item.indexOf(id);
		if(index > -1){
			selected_item.splice(index,1);
		}
		document.getElementById("sales").deleteRow(i);
		document.getElementById("sales").deleteRow(i);
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
	var pay_qty = window.document.getElementById("pay_qty");
	function finishTable()
	{
	if (debugScript)
		window.alert("Beginning of function finishTable");

	var tableElemName = "sales";

	totalVenta = computeTableColumnTotal("sales",6);
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

		updatePago(_total);
	return;
	}

	var payment_type = window.document.getElementById("payment_type").value;
	var pago = window.document.getElementById(payment_type);
	var label = window.document.getElementById("l_"+payment_type);
	var pay_qty = window.document.getElementById("pay_qty").value;
	var table = document.getElementById("paymentsBody");
	var totalPagado = document.getElementById("totalPagado");
	var totalDebe = document.getElementById("totalDebe");
		$(function() {
			var form = document.getElementById("entrega");
			var counter = 0;
			var originalContent = "";
			var originalContentConfirm = "";
			$( "#dialog-confirm" ).dialog({
				dialogClass:"no-close",
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
				open : function(event, ui) {
					originalContentConfirm = "";
				},
				close : function(event, ui) {
					$("#dialog-badcard").html(originalContent);
					$("#dialog-confirm").html(originalContentConfirm);
					console.log(originalContent);
					console.log(originalContentConfirm);
				}
			});
			$( "#dialog-badcard" ).dialog({
				dialogClass:"no-close",
				autoOpen: false,
				resizable: false,
				modal: true,
				buttons: {
					Cancelar: function() {
						$( this ).dialog( "close" );
					}
				},
				open : function(event, ui) {
					originalContent = "";
				},
				close : function(event, ui) {
					$("#dialog-confirm").html(originalContentConfirm);
					$("#dialog-badcard").html(originalContent);
					console.log(originalContent);
					console.log(originalContentConfirm);
				}
			});

			$("#add_pay").click(function(){
				payment_type = window.document.getElementById("payment_type").value;
				pago = window.document.getElementById(payment_type);
				label = window.document.getElementById("l_"+payment_type);
				pay_qty = window.document.getElementById("pay_qty").value;
				table = document.getElementById("paymentsBody");
				totalPagado = document.getElementById("totalPagado");
				totalDebe = document.getElementById("totalDebe");
				var totDeb = 0;

				if(payment_type == 'Gift Card'){
					ask_gift_card();
					var data = "term="+giftCard;
					$.ajax({
						type: "GET",
						dataType: "json",
						url: "sales/giftcardsnumbers", //Relative or absolute path to response.php file
						data: data,
						success: function(data) {
							data.forEach(function(entry) {
								console.log(entry);
								giftCardNumber = entry.number;
								giftCardValue = entry.value;
								if(entry.deleted == 1){
									title="Tarjeta Cancelada.",
									$( "#dialog-badcard" ).dialog( "option", "title", title );
									$( "#dialog-badcard" ).dialog( "open" );
									$( ".ui-dialog-content" ).append("<i class='fa fa-warning fa-3x'></i> ");
									$( ".ui-dialog-content" ).append("<p class='text-center'>La tarjeta <b>"+giftCardNumber+"</b> esta cancelada</p>");
									return;
								}else{
									title="Tarjeta Aceptada."
									$( "#dialog-badcard" ).dialog( "option", "title", title );
									$( "#dialog-badcard" ).dialog( "open" );
									$( ".ui-dialog-content" ).append("<i class='fa fa-thumbs-o-up fa-3x'></i>");
									$( ".ui-dialog-content" ).append("<p class='text-left'>Tarjeta Número: <b>"+giftCardNumber+"</b></p>");
									$( ".ui-dialog-content" ).append("<p class='text-left'>Nombre(s): <b>"+entry.first_name+"</b></p>");
									$( ".ui-dialog-content" ).append("<p class='text-left'>Apellido(s): <b>"+entry.last_name+"</b></p>");
									$( ".ui-dialog-content" ).append("<p class='text-left'>Monto en la tarjeta: <b>"+entry.value+"</b></p>");
									$( ".ui-dialog-content" ).append("<p class='text-left'>Monto a descontar: <b>"+pay_qty+"</b></p>");
									$( ".ui-dialog-content" ).append("<p class='text-left'><b>Desea continuar con la operación?</b></p>");
									$( "#dialog-badcard" ).dialog( "option", "buttons", [ { text: "Aceptar", click: function() { $( this ).dialog( "close" ); valida(); } }, { text: "Cancelar", click: function() { $( this ).dialog( "close" ); } } ] );
									return;
								}
								function valida(){
									pago = window.document.getElementById(payment_type+': '+giftCardNumber);
									if(pago){
										var nuevoValor = parseFloat(pago.value);
									}else {
										var nuevoValor = 0;
									}
									var giftCardResult = parseFloat(giftCardValue) - parseFloat(pay_qty) - nuevoValor;
									label = window.document.getElementById("l_"+payment_type+': '+giftCardNumber);
									console.log(pago);
									if(giftCardResult >= 0){
										if(pago){
											pago.value = parseFloat(pago.value) + parseFloat(pay_qty);
											label.innerHTML = $.number(pago.value,2);
											totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
											totalPagado.innerHTML = $.number(pago.value,2);
										}else{
											var row = table.insertRow(0);
											var cell1 = row.insertCell(0);
											var cell2 = row.insertCell(1);
											var cell3 = row.insertCell(2);
											var cell4 = row.insertCell(3);
											cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totDeb+')" class="button alert tiny">';
											cell2.innerHTML = payment_type + ': ' + giftCardNumber + '<input type="hidden" value="'+payment_type+': '+giftCardNumber+'" />' ;
											cell3.innerHTML = '<div align="right"><label id="l_'+payment_type+': '+giftCardNumber+'">'+ $.number(pay_qty,2) + '</label><input type="hidden" value="'+pay_qty+'" name="payment['+giftCardNumber+']" id="'+payment_type+': '+giftCardNumber+'"/></div>' ;
											cell4.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totDeb+')" class="button alert tiny">';
											counter =+ 1;
											totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
											totalPagado.innerHTML =  $.number(totPayment,2);
										}
										totDeb = _total - totPayment;
										totalDebe.innerHTML =  $.number((_total - totPayment),2);
										document.getElementById("pay_qty").value =  parseFloat(_total) - parseFloat(totPayment);
										console.log(parseFloat(_total) - parseFloat(totPayment));
									}else{
										alert("El saldo de la tarjeta es insuficiente por " + giftCardResult);
										control = 0;
									}
								}
							});
						},
					});

				}else if(pago){
					pago.value = parseFloat(pago.value) + parseFloat(pay_qty);
					label.innerHTML = $.number(pago.value,2);
					totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
					totalPagado.innerHTML = $.number(pago.value,2);
				}else{
					var row = table.insertRow(0);
					var cell1 = row.insertCell(0);
					var cell2 = row.insertCell(1);
					var cell3 = row.insertCell(2);
					var cell4 = row.insertCell(3);
					cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totDeb+')" class="button alert tiny">';
					cell2.innerHTML = payment_type + '<input type="hidden" value="'+payment_type+'" />' ;
					cell3.innerHTML = '<div align="right"><label id="l_'+payment_type+'">'+ $.number(pay_qty,2) + '</label><input type="hidden" value="'+pay_qty+'" name="payment['+payment_type+']" id="'+payment_type+'"/></div>' ;
					cell4.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totDeb+')" class="button alert tiny">';
					counter =+ 1;
					totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
					totalPagado.innerHTML =  $.number(totPayment,2);
				}
				totDeb = _total - totPayment;
				totalDebe.innerHTML =  $.number((_total - totPayment),2);
				document.getElementById("pay_qty").value =  parseFloat(_total) - parseFloat(totPayment);
				console.log(parseFloat(_total) - parseFloat(totPayment));
			});
			$("#submit").click(function(){
				var tipo = window.document.getElementById("right-label").value;
				var customer_id = window.document.getElementById("customer_id").value;
				var totalVenta = computeTableColumnTotal("sales",6);
				var totalVenta = _total;
				//var totalVenta = window.document.getElementById("totalVenta").value;
				var pay_qty = window.document.getElementById("pay_qty").value;
				var pay_qty = totPayment;
				var dif;
				if(tipo == 0){
					tipo = "Venta";
				}else{
					tipo = "Devolución";
				}

				if(customer_id == ''){
					dif = parseFloat(pay_qty) - parseFloat(totalVenta) ;
					if(dif < 0 || isNaN(dif)){
						alert('Existe una diferencia de $ '+dif+' entre el total y la cantidad recibida.\n\nDebe seleccionar un cliente para crédito o cubrir la diferencia.')
					}else{
						title="¿Procesar "+tipo+"?",
						$( "#dialog-confirm" ).dialog( "option", "title", title );
						$( ".ui-dialog-content" ).append("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Una vez procesado, no podrá deshacerse.</p>");
						$( "#dialog-confirm" ).dialog( "open" );
					}
				}else{
					dif = parseFloat(pay_qty) - parseFloat(totalVenta) ;
					if(isNaN(dif)){
						alert('Existe una diferencia de $ '+dif+' entre el total y la cantidad recibida.\n\nDebe seleccionar un cliente para crédito o cubrir la diferencia.')
					}else{
						title="¿Procesar "+tipo+"?",
						$( "#dialog-confirm" ).dialog( "option", "title", title );
						$( ".ui-dialog-content" ).append("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Una vez procesado, no podrá deshacerse.</p>");
						$( "#dialog-confirm" ).dialog( "open" );
					}
				}
			});
		});

	function ask_gift_card() {
		giftCard = prompt("Por favor escriba el número de tarjeta");
		return giftCard;
	}

	function updatePago(x){
		var totalPagado = document.getElementById("totalPagado");
		var totalDebe = document.getElementById("totalDebe");
		totalDebe.innerHTML = $.number((parseFloat(x) - parseFloat(totPayment)),2);
		window.document.getElementById("pay_qty").value = parseFloat(_total) - parseFloat(totPayment);
	//	alert(totalDebe.innerHTML);
	}

	function deleteRowPayment(r,totDeb) {
		var i = r.parentNode.parentNode.rowIndex;
		var table = document.getElementById("payments");
		var str = table.rows[i].cells[1].innerHTML;
		var res = str.split("<",1);
		var cell = document.getElementById(res).value;
		totPayment = parseFloat(totPayment) - parseFloat(cell);

		document.getElementById("totalPagado").innerHTML = $.number(totPayment,2);

		updatePago(_total);
		document.getElementById("payments").deleteRow(i);
	}


	</script>
@stop
