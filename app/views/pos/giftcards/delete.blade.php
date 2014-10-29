@extends('layouts.modal')

{{-- Content --}}
@section('content')

	{{-- Delete User Form --}}
	<form id="deleteForm" method="post" action="@if (isset($giftcards)){{ URL::to('pos/giftcards/' . $giftcards->id . '/delete') }}@endif" autocomplete="off">
		<!-- CSRF Token -->
		<hr>
		<h4>Borrar Tarjeta de Regalo Número: <b>{{ $giftcards->number." ".$giftcards->last_name }}</b> </h4>
		<h4>Perteneciente a: <b>{{ $peoples->first_name." ".$peoples->last_name }}</b> </h4>
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<input type="hidden" name="id" value="{{ $giftcards->id }}" />
		<!-- ./ csrf token -->

		<!-- Form Actions -->
				<element class="button close_popup">Cancel</element>
				<button type="submit" class="button alert">Delete</button>
		<!-- ./ form actions -->
	</form>
@stop
