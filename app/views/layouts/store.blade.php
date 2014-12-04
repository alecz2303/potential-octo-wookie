<!DOCTYPE html>
<html lang="en">
<head>
	<title>
		@section('title')
			Kerberos POS
		@show
	</title>
	<meta name="keywords" content="POS, Kerberos, IT, Services, Point, of, Sale, punto, de, venta" />
	<meta name="author" content="Alejandro Fedle Rueda Jimenez" />
	<meta name="description" content="Punto de venta para tiendas, negocios pequeños, creditos" />

	<!-- Mobile Specific Metas
	================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{{asset('foundation/css/normalize.css')}}">
    <link rel="stylesheet" href="{{asset('foundation/css/foundation.min.css')}}">
    <script src="{{asset('foundation/js/vendor/modernizr.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/colorbox.css')}}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.min.css') }}">

	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/foundation/dataTables.foundation.css">
	<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.tablesTools.css')}}">
	@yield('styles')
    <link rel="stylesheet" href="{{asset('foundation/css/responsive-tables.css')}}">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!-- [if lt IE 9] -->
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<!-- [endif] -->
</head>
<body>
	<noscript>
	<h2>Esta página necesita JavaScript para Funcionar Adecuadamente.</h2>
	<style>div { display:none !important; }</style>
	</noscript>

	<div class="header panel clearfix top" style="text-align:center !important">
		<?php
			$configuracion = AppConfig::where('key','=','company')->get();
		?>
		@foreach ($configuracion as $key => $value)
			<span class="tit1">{{$value->value}}</span><span class="tit2">POS</span><br>
		@endforeach
	</div>

	<!-- Container -->
	<div class="main-section">
		<div class="wrap">
			<div class="container">
				<!-- Notifications -->
				@include('notifications')
				<!-- ./ notifications -->
				<!-- Content -->
				@yield('content')
				<!-- ./ content -->
		    </div>
		</div>
	</div>
	<!-- ./ container -->
	<div id="footer" class="footer callout">
	</div>

	<!-- Javascripts
	================================================== -->
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{asset('foundation/js/foundation.min.js')}}"></script>
	<script src="{{asset('foundation/js/responsive-tables.js')}}"></script>
	<script src="{{asset('js/jquery.colorbox.js')}}"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>
	<script src="{{asset('js/dataTables.tableTools.js')}}"></script>
    <script src="{{asset('js/sticky-footer.js')}}"></script>
	<script src="{{asset('js/jquery-ui.min.js')}}"></script>
	<script type="text/javascript">
		$.fn.dataTable.TableTools.defaults.aButtons = [
			{
				"sExtends": "copy",
				"sButtonText": "Copiar al portapapeles"
			},
			{
				"sExtends": "print",
				"sButtonText": "Imprimir"
			},
			{
				"sExtends":    "collection",
				"sButtonText": "Guardar",
				"aButtons":    [ "csv", "xls", "pdf" ]
			}
		];
		$.extend( $.fn.DataTable.defaults, {
			responsive:true,
			displayLength: 5,
			"pageLength": 5000,
			lengthMenu: [[-1, 5, 10, 25, 50, 100], ["Todos", 5, 10, 25, 50, 100]],
			language: {
				"sLengthMenu": "Mostrar _MENU_ ",
				"sInfo": "Mostrando _START_ al _END_ de _TOTAL_ registros",
				"sSearch": "Buscar:",
				"paginate": {
			        "first":      "Primera",
			        "last":       "Última",
			        "next":       "Siguiente",
			        "previous":   "Anterior"
				},
			},
			"bProcessing": true,
			"bServerSide": false,
			"fnDrawCallback": function ( oSettings ) {
				$(".iframe").colorbox({iframe:true, width:"100%", height:"100%"});
				$(".iframe1").colorbox({iframe:true, width:"100%", height:"100%"});
				$(".iframe2").colorbox({iframe:true, width:"100%", height:"100%"});
			},
			dom: 'T<"wrapper"flit>p',
		});
	</script>
    @yield('scripts')
    <script>
		$(document).foundation();
	</script>
</body>
</html>