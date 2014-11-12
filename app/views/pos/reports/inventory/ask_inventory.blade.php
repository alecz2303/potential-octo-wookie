@extends('layouts.default')
@section('title')
{{{ $title }}} :: @parent
@stop
@section('content')
  <div class="row">
    <div class="large-12 columns">
      <div class="panel">
        <h1>{{$title}}</h1>
      </div>
    </div>
  </div>
  <hr>
  {{Form::open()}}
  <div class="row">
    <div class="large-12 columns">
      <h3>Exportar a PDF?</h3>
      <div class="switch">
        <input id="savePDF" type="checkbox" name="savePDF">
        <label for="savePDF"></label>
      </div>
    </div>
  </div>
  <div class="row">
    <button type="submit" class="button success">OK</button>
  </div>
  {{Form::close()}}

@stop
