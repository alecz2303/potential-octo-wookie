@extends('layouts.default')
@section('content')
	{{ Confide::makeLoginForm()->render() }}
@stop