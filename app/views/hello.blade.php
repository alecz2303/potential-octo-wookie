@extends('layouts.default')
@section('styles')

body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center !important;
			color: #ffffff !important;
		}
.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
@stop
@section('content')
	<div class="welcome">
		<a href="http://kerberosits.esy.es" title="Kerberos POS"><img src="{{asset('img/logo_alex.png')}}" alt=""></a>
		<h1>Kerberos POS.</h1>
	</div>
@stop
