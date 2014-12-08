@extends('layouts.modal')
@section('content')
	<hr>
	<div class="row">
	<div class="large-12 columns">
		<div class="panel">
			<div class="row">
				<div class="small-6 columns">
					<label>Debe:</label>
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="totalDebe">{{round($dif,2)}}</label></b>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="small-6 columns">
					<label>Pagado:</label>
				</div>
				<div class="small-6 columns" align="right">
					<b><label id="totalPagado"></label></b>
				</div>
			</div>
		</div>
		<table id="payments_table" class="dataTable cell-border display compact responsive">
			<caption>Pagos Realizados</caption>
			<thead>
				<tr>
					<th>Fecha</th>
					<th>Tipo de pago</th>
					<th>Cantidad</th>
				</tr>
			</thead>
			<tbody>
				@foreach($sales_payments as $key => $value)
					<tr>
						<td>{{$value->created_at}}</td>
						<td>{{$value->payment_type}}</td>
						<td>$ {{number_format($value->payment_amount,2)}}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div><hr>

	{{Form::open(['id'=>'entrega','data-abide'])}}
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
				Cantidad a abonar:
			</div>
			<div class="small-8 columns">
				<label>
					{{Form::text('pay_qty',0,array('id'=>'pay_qty','required','pattern'=>'number'))}}
				</label>
				<small class="error">Solo se permiten números.</small>
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
					<a id="close_popup" href="#" class="button tiny alert close_popup">Cancelar</a>
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
						</tr>
					</thead>
					<tbody id="paymentsBody">
					</tbody>
				</table>
			</div>
		</div>

		<div id="dialog-confirm" title="">
		</div>

		<div id="dialog-badcard" title="">
		</div>
	{{Form::close()}}
@stop
@section('scripts')
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.css') }}">
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	<script src="{{asset('js/jquery.number.min.js')}}"></script>
	<script charset="utf-8">
		var payment_type = window.document.getElementById("payment_type").value;
		var pago = window.document.getElementById(payment_type);
		var label = window.document.getElementById("l_"+payment_type);
		var pay_qty = window.document.getElementById("pay_qty").value;
		var table = document.getElementById("paymentsBody");
		var totalPagado = document.getElementById("totalPagado");
		var totalDebe = document.getElementById("totalDebe");
		var totPayment = 0;
		var _total = {{$dif}};
		var totDeb = {{$dif}};
		var form = document.getElementById("entrega");
		$(function() {
			var originalContent;
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
					originalContent = $("#dialog-badcard").html();
				},
				close : function(event, ui) {
					$("#dialog-confirm").html(originalContent);
					$("#dialog-badcard").html(originalContent);
				}
			});
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
					originalContent = $("#dialog-confirm").html();
				},
				close : function(event, ui) {
					$("#dialog-badcard").html(originalContent);
					$("#dialog-confirm").html(originalContent);
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

				if(parseFloat(pay_qty) > parseFloat(totDeb)){
					alert('La deuda es menor al pago.');
					return;
				}

				if(payment_type == 'Gift Card'){
					ask_gift_card();
					var data = "term="+giftCard;
					$.ajax({
						type: "GET",
						dataType: "json",
						url: "../../../giftcardsnumbers", //Relative or absolute path to response.php file
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
									$( "#dialog-badcard" ).dialog( "option", "buttons", [ { text: "Aceptar", click: function() { $( this ).dialog( "close" );  valida(); } }, { text: "Cancelar", click: function() { $( this ).dialog( "close" ); } } ] );
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
											cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totDeb+')" class="button alert tiny">';
											cell2.innerHTML = payment_type + ': ' + giftCardNumber + '<input type="hidden" value="'+payment_type+': '+giftCardNumber+'" />' ;
											cell3.innerHTML = '<div align="right"><label id="l_'+payment_type+': '+giftCardNumber+'">'+ $.number(pay_qty,2) + '</label><input type="hidden" value="'+pay_qty+'" name="payment['+giftCardNumber+']" id="'+payment_type+': '+giftCardNumber+'"/></div>' ;
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
					cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totDeb+')" class="button alert tiny">';
					cell2.innerHTML = payment_type + '<input type="hidden" value="'+payment_type+'" />' ;
					cell3.innerHTML = '<div align="right"><label id="l_'+payment_type+'">'+ $.number(pay_qty,2) + '</label><input type="hidden" value="'+pay_qty+'" name="payment['+payment_type+']" id="'+payment_type+'"/></div>' ;
					counter =+ 1;
					totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
					totalPagado.innerHTML =  $.number(totPayment,2);
				}
				totDeb = _total - totPayment;
				totalDebe.innerHTML =  $.number((_total - totPayment),2);
				document.getElementById("pay_qty").value =  parseFloat(_total) - parseFloat(totPayment);
				console.log(parseFloat(_total) - parseFloat(totPayment));
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

		$("#submit").click(function(){
			var tipo = 0;
			var customer_id = {{$customer_id}};
			var totalVenta = _total;
			//var totalVenta = window.document.getElementById("totalVenta").value;
			var pay_qty = window.document.getElementById("pay_qty").value;
			var pay_qty = totPayment;
			var deuda = {{$dif}};

			if(pay_qty > deuda){
				alert("El pago es mayor a la deuda.");
				return;
			}else if(pay_qty == 0){
				alert("No se ha asignado ningun pago.");
				return;
			}

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
					title="¿Procesar Abono a Cuenta?",
					$( "#dialog-confirm" ).dialog( "option", "title", title );
					$( ".ui-dialog-content" ).append("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Una vez procesado, no podrá deshacerse.</p>");
					$( "#dialog-confirm" ).dialog( "open" );
				}
			}else{
				dif = parseFloat(pay_qty) - parseFloat(totalVenta) ;
				if(isNaN(dif)){
					alert('Existe una diferencia de $ '+dif+' entre el total y la cantidad recibida.\n\nDebe seleccionar un cliente para crédito o cubrir la diferencia.')
				}else{
					title="¿Procesar Abono a Cuenta?",
					$( "#dialog-confirm" ).dialog( "option", "title", title );
					$( ".ui-dialog-content" ).append("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Una vez procesado, no podrá deshacerse.</p>");
					$( "#dialog-confirm" ).dialog( "open" );
				}
			}
		});

	</script>
	<script>
		$('#payments_table').DataTable({
			searching: false,
			tableTools: {
				"sSwfPath": "{{URL::asset('swf/copy_csv_xls_pdf.swf')}}"
			}
		});
	</script>

@stop
