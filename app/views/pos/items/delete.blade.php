@extends('layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" method="post" action="@if (isset($items)){{ URL::to('pos/items/' . $items->id . '/delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <hr>
        <h4>Borrar <b>{{ $items->name }}</b> </h4>
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $items->id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
                <element class="button close_popup">Cancel</element>
                <button type="submit" class="button alert">Delete</button>
        <!-- ./ form actions -->
    </form>
@stop