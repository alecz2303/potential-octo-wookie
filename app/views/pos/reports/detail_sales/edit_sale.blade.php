@extends('layouts.modal')

@section('content')
	<hr>
	{{ Form::open(array('data-abide', 'autocomplete'=>'off'))}}
	<div class="row">
		<!-- supplier_id -->
		<div class="large-4 columns">
			<label>Cliente:
					{{ Form::select('customer_id', array('empty'=>'--')+$customer_options , Input::old('customer_id', isset($sales) ? $sales->customer_id : null),['required'=>'']) }}
			</label>
		</div>
		<!-- supplier_id -->
	</div>

	{{ Form::close() }}
@stop
