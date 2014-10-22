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
			<div class="large-4 columns">
				<label>
					UPC/EAN/ISBN:
					<input type="text" name="item_number" id="item_number" value="{{{ Input::old('item_number', isset($items) ? $items->item_number : null) }}}" />
				</label>
			</div>
			<!-- item_number -->

			<!-- name -->
			<div class="large-4 columns">
				<label>
					Nombre del Artículo:
					<input required type="text" name="name" id="name" value="{{{ Input::old('name', isset($items) ? $items->name : null) }}}" />
				</label>
			</div>
			<!-- name -->

			<!-- category -->
			<div class="large-4 columns">
				<label>
					Categoría:
					<input required type="text" name="category" id="category" value="{{{ Input::old('category', isset($items) ? $items->category : null) }}}" />
				</label>
			</div>
			<!-- category -->
		</div>

		<div class="row">
			<!-- supplier_id -->
			<div class="large-4 columns">
				<label>Proveedor:
						{{ Form::select('supplier_id', array('empty'=>'--')+$supplier_options , Input::old('supplier_id', isset($items) ? $items->supplier_id : null),['required'=>'']) }}
					

				</label>
			</div>
			<!-- supplier_id -->

			<!-- cost_price -->
			<div class="large-4 columns">
				<label>Precio de compra:
					{{ Form::text('cost_price', Input::old('cost_price', isset($items) ? $items->cost_price : null), array('required')) }}
				</label>
			</div>
			<!-- cost_price -->

			<!-- unit_price -->
			<div class="large-4 columns">
				<label>Precio de Venta:
					{{ Form::text('unit_price', Input::old('unit_price', isset($items) ? $items->unit_price : null), array('required')) }}
				</label>
			</div>
			<!-- unit_price -->
		</div>

		<div class="row">
			<!-- items_taxes_name -->
			<div class="large-3 columns">
				<label>Impuesto 1:
					{{ Form::text('items_taxes_name', Input::old('items_taxes_name', isset($items_taxes) ? $items_taxes->name : null)) }}
				</label>
			</div>
			<!-- items_taxes_name -->

			<!-- items_taxes_percent -->
			<div class="large-3 columns">
				<label>Porcentaje:
					{{ Form::text('items_taxes_percent', Input::old('items_taxes_percent', isset($items_taxes) ? $items_taxes->percent : null)) }}
				</label>
			</div>
			<!-- items_taxes_percent -->

			<!-- quantity -->
			<div class="large-3 columns">
				<label>Cantidad en stock:
					{{ Form::text('quantity', Input::old('quantity', isset($item_quantities) ? $item_quantities->quantity : null),array('required')) }}
				</label>
			</div>
			<!-- quantity -->

			<!-- reorder_level -->
			<div class="large-3 columns">
				<label>Stock minimo:
					{{ Form::text('reorder_level', Input::old('reorder_level', isset($items) ? $items->reorder_level : null),array('required')) }}
				</label>
			</div>
			<!-- reorder_level -->
		</div>

		<div class="row">
			<div class="large-12 columns">
				<label>Descripción:
					{{ Form::textarea('description', Input::old('description', isset($items) ? $items->description : null),['rows'=>'2']) }}
				</label>
			</div>
		</div>

		<div class="row">
			<div class="switch round large-4 columns">
				El articulo tiene número de serie
				<input id="is_serialized" type="checkbox" name="is_serialized" {{{ Input::old('is_serialized', ($items->is_serialized==1) ?  "checked" : null) }}}>
				<label for="is_serialized"></label>
			</div>

			<div class="switch round large-4 columns">
				Eliminado
				<input id="deleted" type="checkbox" name="deleted" {{{ Input::old('deleted', $items->deleted==1 ?  "checked" : null) }}}>
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
		minLength: 3,
		select: function(event, ui) {
		$('#category').val(ui.item.value);
		}
		});
	});
</script>
@stop