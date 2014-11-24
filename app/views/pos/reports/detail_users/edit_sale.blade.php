@extends('layouts.modal')

@section('content')
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off'))}}
	<div class="row">
		<div class="small-12 columns">
			Fecha de la Venta:
			<span class="label radius warning">{{isset($sales) ? $sales->created_at : null}}</span>
		</div>
	</div>
	<!-- customer_id -->
	<div class="row">
		<div class="small-12 columns">
			<label>Cliente:
					{{ Form::select('customer_id', array('empty'=>'--')+$customer_options , Input::old('customer_id', isset($sales) ? $sales->customer_id : null),['required'=>'']) }}
			</label>
		</div>
	</div>
	<!-- customer_id -->
	<!-- user_id -->
	<div class="row">
		<div class="small-12 columns">
			<label>Empleado:
					{{ Form::select('user_id', $user_options , Input::old('user_id', isset($sales) ? $sales->user_id : null),['required'=>'']) }}
			</label>
		</div>
	</div>
	<!-- user_id -->
	<!-- comment -->
	<div class="row">
		<div class="small-12 columns">
			<label>Comentario
				{{Form::textArea('comment',Input::old('comment', isset($sales) ? $sales->comment : null),['rows'=>'3'])}}
			</label>
		</div>
	</div>
	<!-- comment -->

	<div clarr="row">
		<div class="small-12 columns">
			<element class="button secondary close_popup">Cancelar</element>
			<button type="submit" class="button success">OK</button>
		</div>
	</div>

	{{ Form::close() }}
@stop
