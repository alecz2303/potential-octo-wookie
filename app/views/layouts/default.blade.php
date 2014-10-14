<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			Kerberos Blog
			@show
		</title>
		<meta name="keywords" content="your, awesome, keywords, here" />
		<meta name="author" content="Jon Doe" />
		<meta name="description" content="Lorem ipsum dolor sit amet, nihil fabulas et sea, nam posse menandri scripserit no, mei." />

		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- CSS
		================================================== -->
		<link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">
		<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="{{asset('foundation/css/normalize.css')}}">
        <link rel="stylesheet" href="{{asset('foundation/css/foundation.min.css')}}">
        <script src="{{asset('foundation/js/vendor/modernizr.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        <link rel="stylesheet" href="{{asset('css/colorbox.css')}}">

		<style>
	        @section('styles')
			@show
		</style>

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!-- [if lt IE 9] -->
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<!-- [endif] -->

		<!-- Favicons ================================================== -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{{ asset('assets/ico/apple-touch-icon-144-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{{ asset('assets/ico/apple-touch-icon-114-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{{ asset('assets/ico/apple-touch-icon-72-precomposed.png') }}}">
		<link rel="apple-touch-icon-precomposed" href="{{{ asset('assets/ico/apple-touch-icon-57-precomposed.png') }}}">
		<link rel="shortcut icon" href="{{{ asset('assets/ico/favicon.png') }}}">
	</head>

	<body>

		<div class="header panel clearfix" style="text-align:left !important">
			Kerberos POS
		</div>

		<!-- Navbar -->
		<div class="sticky">

		<nav class="top-bar" data-topbar role="navigation">
			<ul class="title-area">
				<li class="name">
				</li>
				<!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
    			<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
			</ul>
			<section class="top-bar-section">
				<!-- Right Nav Section -->
				<ul class="right">
					@if (Auth::check())
	                    <li><a href="{{{ URL::to('user') }}}">Logged in as {{{ Auth::user()->username }}}</a></li>
	                    <li><a href="{{{ URL::to('users/logout') }}}">Logout</a></li>
                    @else
	                    <li {{ (Request::is('users/login') ? ' class="active"' : '') }}><a href="{{{ URL::to('users/login') }}}">Login</a></li>
	                    <li {{ (Request::is('users/create') ? ' class="active"' : '') }}><a href="{{{ URL::to('users/create') }}}">{{{ Lang::get('site.sign_up') }}}</a></li>
                    @endif
				</ul>
				<ul class="left">
				@if (Auth::check())
					@if (Auth::user()->hasRole('Admin'))
	        			<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/home') }}}"><span class="fa fa-home"></span> Home</a></li>
						<li class="has-dropdown{{ (Request::is('admin/users*|admin/roles*') ? ' active' : '') }}">
					        <a href="#"><span class="fa fa-gear"></span> Options</a>
					        <ul class="dropdown">
					            <li{{ (Request::is('admin/users*') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin/users') }}}"><span class="fa fa-user"></span> Users</a></li>
								<li{{ (Request::is('admin/roles*') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin/roles') }}}"><span class="fa fa-user"></span> Roles</a></li>
					        </ul>
					    </li>
					@endif
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Clientes</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/items') }}}"><span class="fa fa-tag"></span> Articulos</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-tags"></span> Kits de Articulos</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/suppliers') }}}"><span class="fa fa-briefcase"></span> Proveedores</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Reportes</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-truck"></span> Recepción</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Ventas</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Empleados</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Tarjetas de Regalo</a></li>
					<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Configuracion de la tienda</a></li>
				@endif
    			</ul>
			</section>
		</nav>
		</div>
		<!-- Container -->
		<div class="wrap">
		<div class="container">
			<!-- Notifications -->
			@include('notifications')
			<!-- ./ notifications -->

			<!-- Content -->
			@yield('content')
			<!-- ./ content -->
		<!-- ./ container -->

		<!-- the following div is needed to make a sticky footer -->
		<div id="push"></div>
		</div>
		</div>
		<!-- ./wrap -->


	    <div id="footer" class="callout">
	      <div class="container-footer clearfix left">
	        <div class="muted credit footer-text">Kerberos IT Services.</div>
	      </div>
	      <div class="container-footer clearfix right">
	      	<p><a href="mailto:kerberos.it.s@gmail.com">Contacto</a></p>
	      </div>
	    </div>

		<!-- Javascripts
		================================================== -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="{{asset('foundation/js/foundation.min.js')}}"></script>
        <script src="{{asset('js/sticky-footer.js')}}"></script>
        @yield('scripts')
        <script>
			$(document).foundation();
		</script>
	</body>
</html>
