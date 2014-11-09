@extends('layouts.default')
@section('styles')
<style>
@import url(http://fonts.googleapis.com/css?family=Oswald:400,700|Open+Sans+Condensed:300);

.title-div {
	width:300px;
	height:300px;
	text-align: center;
	background-image: url(../img/logo_alex.png);
	background-size: 180px 180px;
	background-repeat: no-repeat;

}
p {
	margin:0;
	line-height:400px;
}
span{
	font-size: 70px;
	text-shadow: 4px 4px 5px rgba(150, 150, 150, 1);
}
.tit1{
	font-family: 'Oswald', sans-serif;
	color: #000000;
}
.tit2{
	font-family: 'Open Sans Condensed', sans-serif;
	color: #0a5175 !important;
}
</style>
@stop
@section('content')
	<div class="row">
		<div class="large-6 columns">
			<div class="small-6 columns title-div"><p><span class="tit1">KERBEROS</span><span class="tit2">POS</span></p></div>
		</div>
		<div class="large-6 columns">
			{{ Confide::makeLoginForm()->render() }}
		</div>
	</div>
@stop
