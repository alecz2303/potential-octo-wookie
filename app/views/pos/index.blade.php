@extends('layouts.default')
@section('styles')
<style media="screen">

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
</style>
@stop
@section('content')
	<div class="row">
		<div class="large-12 columns">
			<div class="small-12 columns title-div"><p><span class="tit1">KERBEROS</span><span class="tit2">POS</span></p></div>
		</div>
	</div>
@stop
