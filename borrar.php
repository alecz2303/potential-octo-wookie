<script type="text/javascript">
	var table;
	var selected_item = [];
	var counter = 0;
	var source;
	var data;

	$(document).ready(function() {

		$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});


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
									celda2.innerHTML = '<small>Número de serie:</small><input type="text"  value="'+entry.is_serialized+'" id="serialnumber_'+counter+'" name="entry['+counter+'][serialnumber]" />';
									celda2.colSpan = 3;
								}
								var row = table.insertRow(0);
								var cell1 = row.insertCell(0);
								var cell2 = row.insertCell(1);
								var cell3 = row.insertCell(2);
								var cell4 = row.insertCell(3);
								var cell5 = row.insertCell(4);
								var cell6 = row.insertCell(5);
								var cell7 = row.insertCell(6);
								cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
								cell2.innerHTML = entry.name + '<input type="hidden" value="'+entry.id+'" name="entry['+counter+'][item]"/>' ;
								cell3.innerHTML = entry.qty;
								cell4.innerHTML = entry.cost;
								cell5.innerHTML = '<input type="text" value="'+entry.kitqty+'" id="qty_'+counter+'" name="entry['+counter+'][quantity]" onchange="total('+counter+','+entry.cost+')" />';
								cell6.innerHTML = '<input type="text" value="0" id="desc_'+counter+'" name="entry['+counter+'][desc]" onchange="total('+counter+','+entry.cost+')" />';
								cell7.innerHTML =  (entry.cost) * entry.kitqty ;
								cell7.id = counter;
								$('#item_name').val('');
								counter += 1;
								selected_item.push(entry.id)
								finishTable();
								return false;
							}else{
								alert('Ya se ha seleccionado este artículo. '+entry.name);
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
									celda2.innerHTML = '<small>Número de serie:</small><input type="text"  value="'+entry.is_serialized+'" id="serialnumber_'+counter+'" name="entry['+counter+'][serialnumber]" />';
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
								var cell7 = row.insertCell(6);
								cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+entry.id+')" class="button alert tiny">';
								cell2.innerHTML = entry.name + '<input type="hidden" value="'+entry.id+'" name="data['+counter+'][item]"/>' ;
								cell3.innerHTML = entry.qty;
								cell4.innerHTML = entry.cost;
								cell5.innerHTML = '<input type="text" value="1" id="qty_'+counter+'" name="data['+counter+'][quantity]" onchange="total('+counter+','+entry.cost+')" />';
								cell6.innerHTML =  '<input type="text" value="0" id="desc_'+counter+'" name="entry['+counter+'][desc]" onchange="total('+counter+','+entry.cost+')" />';
								cell7.innerHTML =  (entry.cost) * 1 ;
								cell7.id = counter;
								$('#item_name').val('');
								counter += 1;
								selected_item.push(entry.id)
								finishTable();
								return false;
							}else{
								alert('Ya se ha seleccionado este artículo. '+entry.name);
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
	alert(i);
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


		var subTotal = totalVenta;
		var ivaVenta = (totalVenta * .16);
		_total = subTotal + ivaVenta;

		subtotalVentaElem.innerHTML = "$ " + $.number(subTotal,2);
		ivaVentaElem.innerHTML = "$ " + $.number(ivaVenta,2);
		totalVentaElem.innerHTML = "$ " + $.number(_total,2);

	}
	catch (ex)
	{
		window.alert("Exception in function finishTable()\n" + ex);
	}

	updatePago(_total);
return;
}
function deleteRowPayment(r,totPayment,totDeb) {
	var i = r.parentNode.parentNode.rowIndex;
	//document.getElementById("payments").deleteRow(i);
	var table = document.getElementById("payments");
	var str = table.rows[i].cells[1].innerHTML;
	var res = str.split("<",1);
	var cell = document.getElementById(res).value;
	document.getElementById("totalPagado").innerHTML = parseFloat(totPayment) - parseFloat(cell);

	alert(totPayment);
	updatePago(_total);
}

function updatePago(x){
	var totalPagado = document.getElementById("totalPagado");
	var totalDebe = document.getElementById("totalDebe");

	totalDebe.innerHTML = $.number((x - totalPagado.innerHTML),2);
	window.document.getElementById("pay_qty").value = _total - totalPagado.innerHTML;
//	alert(totalDebe.innerHTML);
}

var totPayment = 0;
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
		$( "#dialog-confirm" ).dialog({
			dialogClass:"no-close",
			autoOpen: false,
			resizable: false,
			height:200,
			width:300,
			modal: true,
			buttons: {
				"Procesar": function() {
					form.submit();
					$( this ).dialog( "close" );
				},
				Cancelar: function() {
					$( this ).dialog( "close" );
				}
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

			if(pago){
				pago.value = parseFloat(pago.value) + parseFloat(pay_qty);
				label.innerHTML = "$ " + $.number(pago.value,2);
				totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
				totalPagado.innerHTML = "$ " + $.number(pago.value,2);
			}else{
				var row = table.insertRow(0);
				var cell1 = row.insertCell(0);
				var cell2 = row.insertCell(1);
				var cell3 = row.insertCell(2);
				cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRowPayment(this,'+totPayment+','+totDeb+')" class="button alert tiny">';
				cell2.innerHTML = payment_type + '<input type="hidden" value="'+payment_type+'" name="data['+payment_type+']" />' ;
				cell3.innerHTML = '<label id="l_'+payment_type+'">$ '+ $.number(pay_qty,2) + '</label><input type="text" value="'+pay_qty+'" name="data['+counter+'][pay_qty]" id="'+payment_type+'"/>' ;
				counter =+ 1;
				totPayment = parseFloat(totPayment) + parseFloat(pay_qty);
				totalPagado.innerHTML = "$ " + $.number(totPayment,2);
			}
			totDeb = _total - totPayment;
			totalDebe.innerHTML = "$ " + $.number((_total - totPayment),2);
			document.getElementById("pay_qty").value =  parseFloat(_total) - parseFloat(totPayment);
			console.log(parseFloat(_total) - parseFloat(totPayment));
		});
		$("#submit").click(function(){
			var tipo = window.document.getElementById("right-label").value;
			var customer_id = window.document.getElementById("customer_id").value;
			var totalVenta = computeTableColumnTotal("sales",6);
			//var totalVenta = window.document.getElementById("totalVenta").value;
			var pay_qty = window.document.getElementById("pay_qty").value;
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
					$( "#dialog-confirm" ).dialog( "open" );
				}
			}else{
				dif = parseFloat(pay_qty) - parseFloat(totalVenta) ;
				if(isNaN(dif)){
					alert('Existe una diferencia de $ '+dif+' entre el total y la cantidad recibida.\n\nDebe seleccionar un proveedor para crédito o cubrir la diferencia.')
				}else{
					title="¿Procesar "+tipo+"?",
					$( "#dialog-confirm" ).dialog( "option", "title", title );
					$( "#dialog-confirm" ).dialog( "open" );
				}
			}
		});
	});

</script>
