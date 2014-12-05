@extends('layouts.store')

@section('content')
	<hr>
	<div class="row center">
		<h2>Gracias por tu pedido <br>
			{{ $nombre.' '.$ap_pat.' '.$ap_mat }}
		</h2>
		<p>Pronto tendras noticias nuestras...</p>
	</div>
	<hr>
	<div class="row center">
		<a href="{{ URL::to('store/') }}" class="button round">Hacer un pedido nuevo</a>
	</div>
@stop