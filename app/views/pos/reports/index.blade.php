@extends('layouts.default')
{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

@section('content')
	<h2 align="center">
		{{ $title }}
	</h2>
@stop
