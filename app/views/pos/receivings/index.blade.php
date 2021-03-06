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
	<h2 align="center">
		{{{ $title }}}
	</h2>
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off', 'id'=>'entrega'))}}
	<div class="row">
		<div class="small-9 columns" id='left'>
			<div class="row">
				<div class="small-3 columns">
		            <label for="right-label" class="right">Modo de entradas:</label>
		        </div>
				<div class="small-5 columns">
		          	{{Form::select('tipo', array('0' => 'Recepción', '1' => 'Devolución'),0,array('id'=>'right-label'))}}
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
					<a href="{{{ URL::to('pos/items/create') }}}" class="button tiny iframe"><span class="fa fa-plus"></span> Nuevo Artículo</a>
				</div>
			</div>
			<div class="row">
				<table id="receivings" class="cell-border display compact responsive dataTable">
					<thead>
						<tr>
							<th>Borrar</th>
							<th>Nombre Art.</th>
							<th>Inventario</th>
							<th>Costo</th>
							<th>Cant.</th>
							<th>Total</th>
							<th>Borrar</th>
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
					Seleccionar Proveedor
				</label>
				{{ Form::text('supplier_name', null, array('id'=>'supplier_name'))}}
				<div class="hidden" id="divHidden">
					<a href="#" class="button tiny alert" id="delSupplier" onclick="delSupplier()"><span class="fa fa-minus"></span> Quitar Proveedor</a>
				</div>
				{{ Form::hidden('supplier_id', null, array('id'=>'supplier_id'))}}
				<a href="{{{ URL::to('pos/suppliers/create') }}}" class="button tiny iframe"><span class="fa fa-plus"></span> Nuevo Proveedor</a>
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
					{{Form::select('payment_type', array('Efectivo' => 'Efectivo', 'Cheque' => 'Cheque', 'Tarjeta de Débito' => 'Tarjeta de Débito', 'Tarjeta de Crédito' => 'Tarjeta de Crédito'),array('id'=>'right-label'))}}
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
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.css') }}">
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
			source:"receivings/autocomplete",
			select: function(event, ui) {
				if(jQuery.inArray( ui.item.id, selected_item ) < 0){
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
					cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+ui.item.id+')" class="alert tiny">';
					cell1.innerHTML = '<a href="#" onclick="deleteRow(this,'+ui.item.id+')" class="button alert tiny">Borrar</a>';
					console.log(cell1.innerHTML);
					cell2.innerHTML = ui.item.name + '<input type="hidden" value="'+ui.item.id+'" name="data['+counter+'][item]"/>' ;
					cell3.innerHTML = ui.item.qty;
					cell4.innerHTML = ui.item.cost;
					cell5.innerHTML = '<input type="text" value="1" id="qty_'+ui.item.id+'" name="data['+counter+'][quantity]" onchange="total('+ui.item.id+','+ui.item.cost+')" />';
					cell6.innerHTML =  (ui.item.cost) * 1 ;
					cell6.id = ui.item.id;
					cell7.innerHTML = '<a href="#" onclick="deleteRow(this,'+ui.item.id+')" class="button alert tiny">Borrar</a>';
					$('#item_name').val('');
					counter += 1;
					selected_item.push(ui.item.id)
					finishTable();
					return false;
				}else{
					var cantidad = document.getElementById('qty_'+ui.item.id).value;
					cantidad = parseInt(cantidad) + 1;
					document.getElementById('qty_'+ui.item.id).value = cantidad;
					total(ui.item.id,ui.item.cost);
					finishTable();
					//alert('Ya se ha seleccionado este artículo.... '+entry.name+'...'+cantidad);
					$('#item_name').val('');
					return false
				}
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.name + "<br>" + item.description + "<br>" + item.item_number + "</a>" )
				.appendTo( ul );
			};

			$( "#supplier_name" ).autocomplete({
			source: "receivings/suppliers",
			minLength: 0,
			select: function(event, ui) {
				$('#supplier_name').val(ui.item.company_name);
				$('#supplier_id').val(ui.item.id);
				$('#divHidden').toggle();
				document.getElementById('supplier_name').readOnly = true;
				return false;
			}
			})
			.autocomplete( "instance" )._renderItem = function( ul, item ) {
				return $( "<li>" )
				.append( "<a>" + item.company_name + "</a>" )
				.appendTo( ul );
			};
		});
	</script>

	<script>
		function total(x,y){
			var z = document.getElementById("qty_"+x).value;
			document.getElementById(x).innerHTML = z * y;
			finishTable();
		}

		function delSupplier(){
			$('#supplier_name').val('');
			$('#supplier_id').val('');
			document.getElementById('supplier_name').readOnly = false;
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

	   var totalVenta = computeTableColumnTotal("receivings",5);
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
				height:170,
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
				var supplier_id = window.document.getElementById("supplier_id").value;
				var totalVenta = computeTableColumnTotal("receivings",5);
				//var totalVenta = window.document.getElementById("totalVenta").value;
				var pay_qty = window.document.getElementById("pay_qty").value;
				var dif;
				if(tipo == 0){
					tipo = "Recepción";
				}else{
					tipo = "Devolución";
				}

				if(supplier_id == ''){
					dif = parseFloat(pay_qty) - parseFloat(totalVenta) ;
					if(dif < 0 || isNaN(dif)){
						alert('Existe una diferencia de $ '+dif+' entre el total y la cantidad recibida.\n\nDebe seleccionar un proveedor para crédito o cubrir la diferencia.')
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
