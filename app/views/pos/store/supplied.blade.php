@extends('layouts.default')

{{-- Web site Title --}}
@section('title')
	{{{ $title }}} :: @parent
@stop

@section('content')
	
	<h2>
		{{{ $title }}}
	</h2>
	<hr>

	<div class="row">
		<div class="large-12 columns panel callout radius">
			<h1>El pedido fue surtido a:</h1>
			<h2>{{ $people->first_name.' '.$people->last_name }}</h2>
		</div>
	</div>

	<div class="row">
		<div class="small-6 columns">
			@if($people->email != '')
				<a href="{{ URL::to('pos/store/email?email='.$people->email.'&name='.$people->first_name.' '.$people->last_name) }}" class="iframe button expand" id="email">Enviar correo a {{ $people->email }}</a>
				{{ Form::open(array('url'=>'pos/store/email','id'=>'email')) }}
				{{ Form::close() }}
			@else
				<a href="{{ URL::to('pos/customers/'.$people->id.'/edit') }}" class="iframe button expand">Editar datos de Contacto</a>
			@endif
		</div>
		<div class="small-6 columns">
		{{ Form::open(array('url' => 'pos/payments/index')) }}
		{{ Form::hidden('customer_id', $customers->id) }}
			{{ Form::submit('Revisar Deuda',array('class'=>'button expand')) }}
		{{ Form::close() }}
		</div>
	</div>

@stop

@section('scripts')
	<script type="text/javascript">
		$(".iframe").colorbox({
			iframe:true, 
			width:"90%", 
			height:"90%"
		});
		$("#email").click(function(){
			//document.forms["email"].submit();
		});
	</script>
@stop