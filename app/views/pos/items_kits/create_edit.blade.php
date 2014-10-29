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
 .ui-autocomplete-loading {
background: white url('../css/images/loading.gif') right center no-repeat;
}
#city { width: 25em; }
</style>
@stop

@section('content')
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off'))}}
		<!-- Name -->
		<div class="row">
			<div class="large-3 columns">
				<label>Nombre del Kit: <small>Requerido</small>
					{{ Form::text('name', Input::old('name', isset($items_kits) ? $items_kits->name : null), array('required'=>'required'))}}
				</label>
				<small class="error">El nombre es obligatorio.</small>
			</div>
			<div class="large-9 columns">
				<label>Descripción del Kit:
					{{ Form::textarea('description', Input::old('description', isset($items_kits) ? $items_kits->description : null), array('rows'=>2))}}
				</label>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="ui-widget">
				<div class="large-6 columns">
					<label>Agregar Artículo: 
						{{Form::text('buscar', null, ['id'=>'items'])}}
					</label>
				</div>
			</div>
		</div>
		<div class="row">
			<table id="items_table" class="reponsive">
				<tr>
					<th><b>Eliminar</b></th>
					<th><b>Artículo</b></th>
					<th><b>Cantidad</b></th>
				</tr>
				@if(isset($item_kit_items))
				<?php $counter = 0; ?>
					@foreach($item_kit_items as $value)
						<tr>
							<td><input type="button" value="Delete" onclick="deleteRow(this,'{{$value->item_id}}')" class="button alert tiny"></td>
							<td>{{$value->name;}}<input type="hidden" value="{{$value->item_id;}}" name="data[<?php echo $counter; ?>][item]"/></td>
							<td><input type="text" value="{{$value->quantity}}" name="data[<?php echo $counter; ?>][quantity]"/></td>
						</tr>
						<?php $counter += 1; ?>
					@endforeach
				@endif
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
var selected_item = [];
	$(function()
	{
		var mode = "<?php echo $mode; ?>";
		var counter = 0;
		var source
		if(mode==='create'){
			source="autocomplete";
		}else if(mode==='edit'){
			<?php if(isset($item_kit_items)): ?>
				<?php foreach($item_kit_items as $value): ?>
					selected_item.push("<?php echo $value["item_id"]; ?>");
				<?php endforeach; ?>
				source="../autocomplete";
				counter = "<?php echo $counter; ?>";
			<?php endif; ?>
		}
		$( "#items" ).autocomplete({
		minLength: 0,
		source: source,
		select: function(event, ui) {
		    if(jQuery.inArray( ui.item.id, selected_item ) < 0){
				var table = document.getElementById("items_table");
			    var row = table.insertRow(1);
			    if(counter%2!==0){
			    	row.id = 'alt';
			    }
			    var cell1 = row.insertCell(0);
			    var cell2 = row.insertCell(1);
			    var cell3 = row.insertCell(2);
			    cell1.innerHTML = '<input type="button" value="Delete" onclick="deleteRow(this,'+ui.item.id+')" class="button alert tiny">';
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
function deleteRow(r,id) {
    var i = r.parentNode.parentNode.rowIndex;
    id = id.toString();
    var index = selected_item.indexOf(id);
    if(index > -1){
    	selected_item.splice(index,1);
    }
    document.getElementById("items_table").deleteRow(i);
}
</script>
@stop