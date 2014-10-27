@extends('layouts.modal')

@section('styles')
<style>
#items_table {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    width: 100%;
    border-collapse: collapse;
}

#items_table td, #items_table th {
    font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;
}

#items_table th {
    font-size: 1.1em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #ffffff;
}

#alt {
    color: #000000;
    background-color: #EAF2D3;
}
</style>
@stop

@section('content')
	<hr>
	{{ Form::open(array('data-abide'))}}
		<!-- Name -->
		<div class="row">
			<div class="large-3 columns">
				<label>Nombre del Kit: <small>Requerido</small>
					{{ Form::text('name', Input::old('name', isset($items_kits) ? $items_kits->name : null), array('required'=>'required'))}}
				</label>
			</div>
			<div class="large-9 columns">
				<label>Descripción del Kit: <small>Requerido</small>
					{{ Form::textarea('description', Input::old('description', isset($items_kits) ? $items_kits->description : null), array('required','rows'=>2))}}
				</label>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="large-6 columns">
				<label>Agregar Artículo: 
					{{Form::text('buscar', null, ['id'=>'items',])}}
				</label>
			</div>
		</div>
		<div class="row">
			<table id="items_table" class="reponsive">
				<tr>
					<th><b>Eliminar</b></th>
					<th><b>Artículo</b></th>
					<th><b>Cantidad</b></th>
				</tr>
				@foreach($item_kit_items as $value)
				<tr>
					<td>X</td>
					<td>{{$value->item_id;}}</td>
					<td>{{$value->quantity;}}</td>
				</tr>
				@endforeach
			</table>
		</div>
		<div class="row">
		<!-- Form Actions -->
		<element class="button secondary close_popup">Cancelar</element>
		<button type="submit" class="button success">OK</button>
		<!-- ./ form actions -->
		</div>
	{{ Form::close()}}
@stop
@section('scripts')

<script>
	$(function()
	{
		var counter = 0;
		var selected_item = [];
		$( "#items" ).autocomplete({
		source: "autocomplete",
		minLength: 0,
		select: function(event, ui) {
		    if(jQuery.inArray( ui.item.id, selected_item )!==0){
				var table = document.getElementById("items_table");
			    var row = table.insertRow(1);
			    if(counter%2!==0){
			    	row.id = 'alt';
			    }
			    var cell1 = row.insertCell(0);
			    var cell2 = row.insertCell(1);
			    var cell3 = row.insertCell(2);
			    cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this)" class="button alert tiny">';
			    cell2.innerHTML = ui.item.value + '<input type="hidden" value="'+ui.item.id+'" name="data['+counter+'][item]"/>' ;
			    cell3.innerHTML = '<input type="text" value="1" name="data['+counter+'][quantity]"/>';
			    $('#items').val('');
			    counter += 1;
			    selected_item.push(ui.item.id)
				return false;
			}else{
				alert('Ya se ha seleccionado este artículo.');
				$('#items').val('');
				return false
			}
		}
		})
		 .autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
			.append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
			.appendTo( ul );
		};
	});
</script>

<script>
function deleteRow(r) {
    var i = r.parentNode.parentNode.rowIndex;
    document.getElementById("items_table").deleteRow(i);
}
</script>
@stop