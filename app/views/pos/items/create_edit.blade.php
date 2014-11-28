@extends('layouts.modal')
{{-- Content --}}
@section('content')

<hr>
	<form method="post" action="@if (isset($items)) {{ URL::to('pos/items/' . $items->id . '/edit') }}@endif" autocomplete="off" data-abide>
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<!-- ./ csrf token -->

		<div class="row">
			<!-- item_number -->
			<div class="small-4 columns">
				<label>
					UPC/EAN/ISBN:
					<input type="text" name="item_number" id="item_number" value="{{{ Input::old('item_number', isset($items) ? $items->item_number : null) }}}" />
				</label>
			</div>
			<!-- item_number -->

			<!-- name -->
			<div class="small-4 columns">
				<label>
					Nombre del Artículo:
					<input required type="text" name="name" id="name" value="{{{ Input::old('name', isset($items) ? $items->name : null) }}}" />
				</label>
				<small class="error">El Nombre de Artículo es Obligatorio</small>
			</div>
			<!-- name -->

			<!-- category -->
			<div class="small-4 columns">
				<label>
					Categoría:
					<input required type="text" name="category" id="category" value="{{{ Input::old('category', isset($items) ? $items->category : null) }}}" />
				</label>
				<small class="error">Categoria es Obligatorio</small>
			</div>
			<!-- category -->
		</div>

		<div class="row">
			<!-- supplier_id -->
			<div class="small-4 columns">
				<label>Proveedor:
						{{ Form::select('supplier_id', array('empty'=>'--')+$supplier_options , Input::old('supplier_id', isset($items) ? $items->supplier_id : null),['required'=>'']) }}
				</label>
				<small class="error">El Proveedor es Obligatorio</small>
			</div>
			<!-- supplier_id -->

			<!-- cost_price -->
			<div class="small-4 columns">
				<label>Precio de compra:
					{{ Form::number('cost_price', Input::old('cost_price', isset($items) ? $items->cost_price : null), array('required','pattern'=>'number')) }}
				</label>
				<small class="error">El Precio de Compra es Obligatorio y solo números</small>
			</div>
			<!-- cost_price -->

			<!-- unit_price -->
			<div class="small-4 columns">
				<label>Precio de Venta:
					{{ Form::number('unit_price', Input::old('unit_price', isset($items) ? $items->unit_price : null), array('required','pattern'=>'number')) }}
				</label>
				<small class="error">El Precio de Compra es Obligatorio y solo números</small>
			</div>
			<!-- unit_price -->
		</div>

		<div class="row">
			<!-- items_taxes_name -->
			<div class="small-3 columns">
				<label>Impuesto 1:
					{{ Form::text('items_taxes_name', Input::old('items_taxes_name', isset($items_taxes) ? $items_taxes->name : 'IVA')) }}
				</label>
				<small class="error">El Nombre del Impuesto es obligatorio</small>
			</div>
			<!-- items_taxes_name -->

			<!-- items_taxes_percent -->
			<div class="small-3 columns">
				<label>Porcentaje:
					{{ Form::number('items_taxes_percent', Input::old('items_taxes_percent', isset($items_taxes) ? $items_taxes->percent : 0),['pattern'=>'number']) }}
				</label>
				<small class="error">El porcentaje es obligatorio y número</small>
			</div>
			<!-- items_taxes_percent -->

			<!-- quantity -->
			<div class="small-3 columns">
				<label>Cantidad en stock:
					{{ Form::number('quantity', Input::old('quantity', isset($item_quantities) ? $item_quantities->quantity : null),array('required','pattern'=>'number')) }}
				</label>
				<small class="error">La Cantidad en Stock es Obligatorio y solo números</small>
			</div>
			<!-- quantity -->

			<!-- reorder_level -->
			<div class="small-3 columns">
				<label>Stock mínimo:
					{{ Form::number('reorder_level', Input::old('reorder_level', isset($items) ? $items->reorder_level : null),array('required','pattern'=>'number')) }}
				</label>
				<small class="error">El Stock mínimo es Obligatorio y solo números</small>
			</div>
			<!-- reorder_level -->
		</div>

		<div class="row">
			<div class="small-12 columns">
				<label>Descripción:
					{{ Form::textarea('description', Input::old('description', isset($items) ? $items->description : null),['rows'=>'2']) }}
				</label>
			</div>
		</div>

		<div class="row">
			<div class="switch round small-4 columns">
				El articulo tiene número de serie
				<input id="is_serialized" type="checkbox" name="is_serialized" {{{
				Input::old(
					'is_serialized',
					isset($items) ?
						(
							$items->is_serialized==1 ?  "checked" : null
						) : null
				) }}}>
				<label for="is_serialized"></label>
			</div>

			<div class="switch round small-4 columns">
				Eliminado
				<input id="deleted" type="checkbox" name="deleted" {{{
				Input::old(
					'deleted',
					isset($items) ?
						(
							$items->deleted==1 ?  "checked" : null
						) : null
				) }}}>
				<label for="deleted"></label>
			</div>
		</div>

		<div class="row">
		<!-- Form Actions -->
			<element class="button secondary close_popup">Cancelar</element>
			<button type="submit" class="button success">OK</button>
		<!-- ./ form actions -->
		</div>
	</form>
@stop
@section('scripts')
<script>
	$(function()
	{
		$( "#category" ).autocomplete({
		source: "autocomplete",
		minLength: 0,
		select: function(event, ui) {
			$('#category').val(ui.item.value);
		}
		});
	});
</script>
@stop
