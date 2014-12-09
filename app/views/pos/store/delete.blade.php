@extends('layouts.modal')

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" method="post" action="@if (isset($store_orders)){{ URL::to('pos/store/' . $store_orders->id . '/delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <hr>
        <h4>Borrar Pedido en linea </h4>
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $store_orders->id }}" />
        <!-- ./ csrf token -->

        <!-- Form Actions -->
                <element class="button close_popup">Cancelar</element>
                <button type="submit" class="button alert">Eliminar</button>
        <!-- ./ form actions -->
    </form>
@stop