@extends('layouts.modal')

@section('styles')
<style>
.ui-autocomplete-loading {
background: white url('../css/images/loading.gif') right center no-repeat;
}
</style>
@stop

@section('content')
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off'))}}
		<!-- Name -->
		<div class="row">
			<div class="large-6 columns">
				<label>Nombre del Cliente: <small>Requerido</small>
					{{ Form::text('name', Input::old('name', isset($giftcards) ? $giftcards->name : null), array('required'=>'required', 'id'=>'name'))}}
				</label>
				<small class="error">El nombre es obligatorio.</small>
			</div>
			<div class="large-3 columns">
				{{Form::hidden('people_id',Input::old('people_id', isset($giftcards) ? $giftcards->people_id : null), array('id'=>'people_id'))}}
			</div>
		</div>
		<div class="row">
			<div class="large-6 columns">
				<label>Número de Tarjeta de Regalo: <small>Requerido</small>
					{{ Form::text('number', Input::old('number', isset($giftcards) ? $giftcards->number : null), array('required'=>'required', 'pattern'=>'integer', 'id'=>'number'))}}
				</label>
				<small class="error">El número de tarjeta es obligatorio y de tipo númerico.</small>
			</div>
		</div>
		<div class="row">
			<div class="large-6 columns">
				<label>Valor: <small>Requerido</small>
					{{ Form::text('value', Input::old('value', isset($giftcards) ? $giftcards->value : null), array('required'=>'required', 'pattern'=>'number', 'id'=>'value'))}}
				</label>
				<small class="error">El valor es obligatorio y de tipo númerico.</small>
			</div>
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
		var source
		if(mode==='create'){
			source="autocomplete";
		}else if(mode==='edit'){
			<?php if(isset($giftcards)): ?>
				source="../autocomplete";
			<?php endif; ?>
		}
		$( "#name" ).autocomplete({
		minLength: 0,
		source: source,
		select: function(event, ui) {
			$('#people_id').val(ui.item.id);
			$('#name').val(ui.item.first_name+' '+ui.item.last_name);
			return false;
		}
		})
		.autocomplete( "instance" )._renderItem = function( ul, item ) {
			return $( "<li>" )
			.append( "<a><i class='fa fa-user'></i> " + item.first_name + " " + item.last_name + "</a><hr>" )
			.appendTo( ul );
		};
	});
</script>
@stop
