@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
    {{{ $title }}} :: @parent
@stop

{{-- Content --}}
@section('content')
    <div class="page-header">
        <h3>
            {{{ $title }}}

            <div class="pull-right">
                <a href="{{{ URL::to('pos/giftcards/create') }}}" class="button small iframe"><span class="fa fa-plus"></span> Crear</a>
            </div>
        </h3>
    </div>

    <table id="giftcards" class="responsive">
        <thead>
            <tr>
                <th >Apellidos</th>
                <th >Nombre</th>
                <th >Número de tarjeta de Regalo</th>
                <th >Valor</th>
                <th >Acciones</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th >Apellidos</th>
                <th >Nombre</th>
                <th >Número de tarjeta de Regalo</th>
                <th >Valor</th>
                <th ></th>
            </tr>
        </tfoot>
        <tbody>
        </tbody>
    </table>
@stop

@section('scripts')
<!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">

        <!-- DataTables -->
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>

        <script src="{{asset('js/jquery.colorbox.js')}}"></script>

    <script type="text/javascript">
        var table;
        $(document).ready(function() {


            // Setup - add a text input to each footer cell
            $('#giftcards tfoot th').each( function () {
                var title = $('#giftcards thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
            } );

            table = $('#giftcards').DataTable({
                responsive: true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ registros por página"
                },
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "{{ URL::to('pos/giftcards/data') }}",
                "fnDrawCallback": function ( oSettings ) {
                       $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                       $(".iframe1").colorbox({iframe:true, width:"70%", height:"90%"});
                       $(".iframe2").colorbox({iframe:true, width:"40%", height:"50%"});
                 }
            });

            // Apply the search
            table.columns().eq( 0 ).each( function ( colIdx ) {
                $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
                    table
                        .column( colIdx )
                        .search( this.value )
                        .draw();
                } );
            } );
        });
    </script>
@stop
