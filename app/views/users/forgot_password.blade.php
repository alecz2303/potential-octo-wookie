@extends('layouts.default')
@section('content')
	{{ Confide::makeForgotPasswordForm()->render() }}
@stop