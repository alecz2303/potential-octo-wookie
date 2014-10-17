@extends('layouts.default')
@section('content')
	<div class="header panel clearfix" style="text-align:center !important">
		<a class="button right" href="{{{ URL::to('admin/users') }}}"><span class="fa fa-user"></span> Users</a>
		<a class="button left" href="{{{ URL::to('admin/roles') }}}"><span class="fa fa-user"></span> Roles</a>
	</div>
@stop