@extends('layouts.default')
@section('content')
	{{ Confide::makeResetPasswordForm($token)->render() }}
@stop