<!DOCTYPE html>

<html lang="en">

<head>

	<meta charset="UTF-8">

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>
		@section('title')
			{{{ $title }}} :: Administration
		@show
	</title>

	<meta name="keywords" content="@yield('keywords')" />
	<meta name="author" content="@yield('author')" />
	<!-- Google will often use this as its description of your page/site. Make it good. -->
	<meta name="description" content="@yield('description')" />

	<!-- Speaking of Google, don't forget to set your site up: http://google.com/webmasters -->
	<meta name="google-site-verification" content="">

	<!-- Dublin Core Metadata : http://dublincore.org/ -->
	<meta name="DC.title" content="Project Name">
	<meta name="DC.subject" content="@yield('description')">
	<meta name="DC.creator" content="@yield('author')">

	<!--  Mobile Viewport Fix -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<!-- This is the traditional favicon.
	 - size: 16x16 or 32x32
	 - transparency is OK
	 - see wikipedia for info on browser support: http://mky.be/favicon/ -->
	<link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">

	<!-- iOS favicons. -->
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
	<link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{{asset('foundation/css/normalize.css')}}">
    <link rel="stylesheet" href="{{asset('foundation/css/foundation.min.css')}}">
    <script src="{{asset('foundation/js/vendor/modernizr.js')}}"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/1.0.1/css/dataTables.responsive.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/colorbox.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.min.css') }}">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/foundation/dataTables.foundation.css">
	<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.tablesTools.css')}}">

	<style>
	.tab-pane {
		padding-top: 20px;
	}
	</style>

	@yield('styles')

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Asynchronous google analytics; this is the official snippet.
	 Replace UA-XXXXXX-XX with your site's ID and uncomment to enable.
	<script type="text/javascript">
		var _gaq = _gaq || [];
	  	_gaq.push(['_setAccount', 'UA-31122385-3']);
	  	_gaq.push(['_trackPageview']);

	  	(function() {
	   		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  	})();

	</script> -->

</head>

<body>
	<!-- Container -->
	<div class="wrap">
	<div class="container">

		<!-- Notifications -->
		@include('notifications')
		<!-- ./ notifications -->

		<div class="page-header">
			<h3>
				{{ $title }}
				<div class="pull-right">
					<button class="button small close_popup"><span class="fa fa-arrow-circle-left"></span> Back</button>
				</div>
			</h3>
		</div>

		<!-- Content -->
		@yield('content')
		<!-- ./ content -->

		<!-- Footer -->
		<footer class="clearfix">
			@yield('footer')
		</footer>
		<!-- ./ Footer -->

	</div>
	</div>
	<!-- ./ container -->

	<!-- Javascripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.js"></script>
	<script src="{{asset('js/dataTables.tableTools.js')}}"></script>
    <script src="{{asset('assets/js/jquery.colorbox.js')}}"></script>
    <script src="{{asset('foundation/js/foundation.min.js')}}"></script>
    <script src="{{asset('foundation/js/foundation/foundation.tooltip.js')}}"></script>
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>

 <script type="text/javascript">
$(document).ready(function(){
	$('.close_popup').click(function(){
		if(parent.table){
			parent.table.ajax.reload();
		}
		else{
			parent.location.reload();
		}
		parent.jQuery.fn.colorbox.close();
		return false;
	});
	$('#deleteForm').submit(function(event) {
		var form = $(this);
		$.ajax({
		type: form.attr('method'),
		url: form.attr('action'),
		data: form.serialize()
		}).done(function() {
		parent.jQuery.colorbox.close();
		parent.table.ajax.reload();
		}).fail(function() {
		});
		event.preventDefault();
	});
});
</script>
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
		responsive: true,
		displayLength: 5,
		"pageLength": 5000,
		lengthMenu: [[-1, 5, 10, 25, 50, 100], ["Todos", 5, 10, 25, 50, 100]],
		language: {
			"sLengthMenu": "Mostrar _MENU_ registros por página",
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
			$(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
			$(".iframe1").colorbox({iframe:true, width:"70%", height:"90%"});
			$(".iframe2").colorbox({iframe:true, width:"40%", height:"80%"});
		},
		dom: 'T<"clear">lfrtip',
	});
</script>

    @yield('scripts')
<script>
	$(document).foundation();
</script>
</body>

</html>
