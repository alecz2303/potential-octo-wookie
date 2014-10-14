@extends('layouts.default')
@section('content')
	{{ Confide::makeSignUpForm()->render() }}
@stop