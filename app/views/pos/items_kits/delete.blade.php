@extends('layouts.modal')

@section('styles')
<style>
#items_table {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    width: 100%;
    border-collapse: collapse;
}

#items_table td, #items_table th {
    font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;
}

#items_table th {
    font-size: 1.1em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #ffffff;
}

#alt {
    color: #000000;
    background-color: #EAF2D3;
}
 .ui-autocomplete-loading {
background: white url('../css/images/loading.gif') right center no-repeat;
}
</style>
@stop

{{-- Content --}}
@section('content')

    {{-- Delete User Form --}}
    <form id="deleteForm" method="post" action="@if (isset($items_kits)){{ URL::to('pos/items_kits/' . $items_kits->id . '/delete') }}@endif" autocomplete="off">
        <!-- CSRF Token -->
        <hr>
        <h4>Borrar <b>{{ $items_kits->name }}</b> </h4>
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <input type="hidden" name="id" value="{{ $items_kits->id }}" />
        <!-- ./ csrf token -->
        <div class="row">
            <table id="items_table" class="reponsive">
                <tr>
                    <th><b>Artículo</b></th>
                    <th><b>Cantidad</b></th>
                </tr>
                @if(isset($item_kit_items))
                <?php $counter = 0; ?>
                    @foreach($item_kit_items as $value)
                        <tr>
                            <td>{{$value->name;}}</td>
                            <td>{{$value->quantity}}</td>
                        </tr>
                        <?php $counter += 1; ?>
                    @endforeach
                @endif
            </table>
        </div>
        <!-- Form Actions -->
                <element class="button close_popup">Cancel</element>
                <button type="submit" class="button alert">Delete</button>
        <!-- ./ form actions -->
    </form>
@stop