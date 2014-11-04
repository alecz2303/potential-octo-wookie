@extends('layouts.default')
{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop
@section('content')
	<h2 align="center">
		{{{ $title }}}
	</h2>
	<hr>
	{{ Form::open()	}}
		<?php foreach ($app_config as $key => $value): ?>
			<div class="row">
				<label>
					{{$value->key}}
					{{Form::text($value->key,$value->value)}}
				</label>
			</div>
		<?php endforeach ?>
		<div class="row">
			<!-- Form Actions -->
			<button type="submit" class="button success">OK</button>
			<!-- ./ form actions -->
		</div>
	{{ Form::close() }}
@stop
