@extends('layouts.default')
@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
	<div class="row">
	<div class="large-12 columns">
		<div class="panel">
		<h1>{{$title}}</h1>
		</div>
	</div>
	</div>
	<hr>

	{{Form::open()}}
		<div class="row">
			<div class="large-4 columns">
				<label>
					Rango de Fecha:
					<input type="date" name="item_number" id="item_number" value="{{{ Input::old('item_number', isset($items) ? $items->item_number : null) }}}" />
				</label>
			</div>
		</div>
	{{Form::close()}}
@stop
