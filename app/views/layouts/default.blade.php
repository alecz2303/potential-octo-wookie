<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
			Kerberos POS
			@show
		</title>
		<meta name="keywords" content="POS, Kerberos, IT, Services, Point, of, Sale, punto, de, venta" />
		<meta name="author" content="Alejandro Fedle Rueda Jimenez" />
		<meta name="description" content="Punto de venta para tiendas, negocios pequeños, creditos" />

		<!-- Mobile Specific Metas
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		================================================== -->

		<!-- CSS
		================================================== -->
		<link rel="stylesheet" type="text/css" href="{{ asset('font-awesome/css/font-awesome.min.css') }}">
		<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="{{asset('foundation/css/normalize.css')}}">
        <link rel="stylesheet" href="{{asset('foundation/css/foundation.min.css')}}">
        <link rel="stylesheet" href="{{asset('foundation/css/responsive-tables.css')}}">
        <script src="{{asset('foundation/js/vendor/modernizr.js')}}"></script>
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        <link rel="stylesheet" href="{{asset('css/colorbox.css')}}">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.min.css') }}">

		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.4/css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/plug-ins/9dcbecd42ad/integration/foundation/dataTables.foundation.css">
		@yield('styles')

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

		<div class="header panel clearfix" style="text-align:center !important">
			<span class="tit1">KERBEROS</span><span class="tit2">POS</span>
			@if (Auth::check())
                <a class="button tiny right" href="{{{ URL::to('user') }}}">Logged in as {{{ Auth::user()->username }}}</a>
                <a class="button tiny left" href="{{{ URL::to('users/logout') }}}">Logout</a>
            @else
                <a class="button right" href="{{{ URL::to('users/login') }}}">Login</a></li>
            @endif
		</div>

		<div class="off-canvas-wrap" data-offcanvas>
		  	<div class="inner-wrap">
			    <nav class="tab-bar">
			      <section class="left-small">
			        <a class="left-off-canvas-toggle menu-icon" href="#"><span></span></a>
			      </section>

			      <section class="middle tab-bar-section">
			        <h1 class="title">Kerberos IT Services Point of Sale</h1>
			      </section>

			      <section class="right-small">
			        <a class="right-off-canvas-toggle menu-icon" href="#"><span></span></a>
			      </section>
			    </nav>

			    <aside class="left-off-canvas-menu">
			      <ul class="off-canvas-list">
			        <li><label>Configuración</label></li>
			        @if (Auth::check())
						@if (Auth::user()->hasRole('Admin'))
			    			<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/home') }}}"><span class="fa fa-home"></span> Home</a></li>
							<li class="has-submenu{{ (Request::is('admin/users*|admin/roles*') ? ' active' : '') }}">
						        <a href="#"><span class="fa fa-gear"></span> Options</a>
						        <ul class="left-submenu">
						        	<li class="back"><a href="#">Back</a></li>
						            <li{{ (Request::is('admin/users*') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin/users') }}}"><span class="fa fa-user"></span> Users</a></li>
									<li{{ (Request::is('admin/roles*') ? ' class="active"' : '') }}><a href="{{{ URL::to('admin/roles') }}}"><span class="fa fa-user"></span> Roles</a></li>
						        </ul>
						    </li>
						@endif
					@endif
			      </ul>
			    </aside>

			    <aside class="right-off-canvas-menu">
			      <ul class="off-canvas-list">
			        <li><label>Menu</label></li>
			        @if (Auth::check())
						@if (Auth::user()->can('manage_customers'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Clientes</a></li>
						@endif
						@if (Auth::user()->can('manage_items'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/items') }}}"><span class="fa fa-tag"></span> Articulos</a></li>
						@endif
						@if (Auth::user()->can('manage_items_kits'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/items_kits') }}}"><span class="fa fa-tags"></span> Kits de Articulos</a></li>
						@endif
						@if (Auth::user()->can('manage_suppliers'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/suppliers') }}}"><span class="fa fa-briefcase"></span> Proveedores</a></li>
						@endif
						@if (Auth::user()->can('manage_reports'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/reports') }}}"><span class="fa fa-book"></span> Reportes</a></li>
						@endif
						@if (Auth::user()->can('manage_receivings'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/receivings') }}}"><span class="fa fa-truck"></span> Recepción</a></li>
						@endif
						@if (Auth::user()->can('manage_sales'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/sales') }}}"><span class="fa fa-shopping-cart"></span> Ventas</a></li>
						@endif
						@if (Auth::user()->can('manage_payments'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/payments') }}}"><span class="fa fa-money"></span> Abonos a Cuenta</a></li>
						@endif
						@if (Auth::user()->can('manage_gift_cards'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/giftcards') }}}"><span class="fa fa-gift"></span> Tarjetas de Regalo</a></li>
						@endif
						@if (Auth::user()->can('manage_app_config'))
							<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/appconfig') }}}"><span class="fa fa-cogs"></span> Configuracion de la tienda</a></li>
						@endif
					@endif
			      </ul>
			    </aside>

			    <section class="main-section">
				    <!-- Menu Bar -->
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
								<ul class="left">
								@if (Auth::check())
									@if (Auth::user()->can('manage_customers'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/customers') }}}"><span class="fa fa-group"></span> Clientes</a></li>
									@endif
									@if (Auth::user()->can('manage_items'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/items') }}}"><span class="fa fa-tag"></span> Articulos</a></li>
									@endif
									@if (Auth::user()->can('manage_items_kits'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/items_kits') }}}"><span class="fa fa-tags"></span> Kits de Articulos</a></li>
									@endif
									@if (Auth::user()->can('manage_suppliers'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/suppliers') }}}"><span class="fa fa-briefcase"></span> Proveedores</a></li>
									@endif
									@if (Auth::user()->can('manage_reports'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/reports') }}}"><span class="fa fa-book"></span> Reportes</a></li>
									@endif
									@if (Auth::user()->can('manage_receivings'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/receivings') }}}"><span class="fa fa-truck"></span> Recepción</a></li>
									@endif
									@if (Auth::user()->can('manage_sales'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/sales') }}}"><span class="fa fa-shopping-cart"></span> Ventas</a></li>
									@endif
									@if (Auth::user()->can('manage_payments'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/payments') }}}"><span class="fa fa-money"></span> Abonos a Cuenta</a></li>
									@endif
									@if (Auth::user()->can('manage_gift_cards'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/giftcards') }}}"><span class="fa fa-gift"></span> Tarjetas de Regalo</a></li>
									@endif
									@if (Auth::user()->can('manage_app_config'))
										<li{{ (Request::is('admin') ? ' class="active"' : '') }}><a href="{{{ URL::to('pos/appconfig') }}}"><span class="fa fa-cogs"></span> Configuracion de la tienda</a></li>
									@endif
								@endif
				    			</ul>
							</section>
						</nav>
						</div>
				    <!-- //Menu Bar -->


				     <!-- Container -->
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
					<!-- ./ container -->
			    </section>

			  <a class="exit-off-canvas"></a>





				<!-- the following div is needed to make a sticky footer -->
			</div>
		</div>
		<!-- ./wrap -->


	    <div id="footer" class="footer callout">

	    </div>

		<!-- Javascripts
		================================================== -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="{{asset('foundation/js/foundation.min.js')}}"></script>
		<script src="{{asset('foundation/js/responsive-tables.js')}}"></script>
        <script src="{{asset('js/sticky-footer.js')}}"></script>
		<script src="{{asset('js/jquery-ui.min.js')}}"></script>
        @yield('scripts')
        <script>
			$(document).foundation();
		</script>
	</body>
</html>
