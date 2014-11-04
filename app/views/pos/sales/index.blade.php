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
{{-- Content --}}
@section('content')
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
				<table id="receivings" class="dataTable">
					<thead>
						<tr>
							<th >Borrar</th>
							<th >Nombre Art.</th>
							<th >Inventario</th>
							<th >Precio</th>
							<th >Cant.</th>
							<th >%Desc.</th>
							<th >Total</th>
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
					<label>Total:</label>
				</div>
				<div class="small-6 columns">
					<b><label id="totalVenta"></label></b>
				</div>
			</div>
			<div class="row">
				<div class="small-12 columns">
					<label>Comentarios:
						{{Form::textarea('comment',null,array('rows'=>'3'))}}
					</label>
				</div>
			</div>
			<div class="row">
				<div class="small-4 columns">
					Tipo de Pago:
				</div>
				<div class="small-8 columns">
					{{Form::select('payment_type', array('Efectivo' => 'Efectivo', 'Cheque' => 'Cheque', 'Gift Card' => 'Gift Card', 'Tarjeta de Débito' => 'Tarjeta de Débito', 'Tarjeta de Crédito' => 'Tarjeta de Crédito'),array('id'=>'right-label'))}}
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
						<a href="{{{ URL::to('pos/receivings') }}}" class="button tiny alert">Cancelar</a>
						<a id="submit" class="button success tiny" >Terminar</a>
					</div>
				<!-- ./ form actions -->
				</div>
			</div>
		</div>
	</div>

	<div id="dialog-confirm" title="">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Una vez procesado, no podrá deshacerse.</p>
	</div>
	{{ Form::close() }}
@stop

@section('scripts')
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	 <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

	<!-- DataTables -->
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

	<script src="{{asset('js/jquery.colorbox.js')}}"></script>

	<script src="{{asset('js/jquery.number.min.js')}}"></script>

	<script type="text/javascript">
		var table;
		$(document).ready(function() {

			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});


		});
	</script>

	<script>
	var selected_item = [];
		$(function()
		{
			var counter = 0;
			var source
			$( "#item_name" ).autocomplete({
			minLength: 0,
			source:"sales/auto",
			select: function(event, ui) {
				if(ui.item.tipo == 'Kit'){
					var data = "term="+ui.item.id;
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
	</script>

	<script>
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
	</script>

	<script>
	function deleteRow(r,id) {
		var i = r.parentNode.parentNode.rowIndex;
		id = id.toString();
		var index = selected_item.indexOf(id);
		if(index > -1){
			selected_item.splice(index,1);
		}
		document.getElementById("receivings").deleteRow(i);
		finishTable();
	}
	</script>

	<script type="text/javascript">
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
	    for (i=0; i<(howManyRows); i++) // skip first and last row (hence i=1, and howManyRows-1)
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

	function finishTable()
	{
	   if (debugScript)
	     window.alert("Beginning of function finishTable");

	   var tableElemName = "receivings";

	   var totalVenta = computeTableColumnTotal("receivings",6);
	   //var totalMilesHiked = computeTableColumnTotal("hikeTable",3);

	   if (debugScript)
	   {
	      window.alert("totalVenta=" + totalVenta + "\n");
	   }

		try
		{
			var totalVentaElem = window.document.getElementById("totalVenta");
		    totalVentaElem.innerHTML = "<b>$ " + $.number(totalVenta,2)+"</b>";

		}
		catch (ex)
		{
		     window.alert("Exception in function finishTable()\n" + ex);
		}

	   return;
	}
	</script>

	<script>
		$(function() {
			var form = document.getElementById("entrega");
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
			$("#submit").click(function(){
				var tipo = window.document.getElementById("right-label").value;
				var customer_id = window.document.getElementById("customer_id").value;
				var totalVenta = computeTableColumnTotal("receivings",5);
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
@stop
