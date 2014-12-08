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

	{{Form::open(['autocomplete'=>'off'])}}

		<h4>Cliente</h4>
		<div class="row">
			<div class="large-6 columns panel">
				<div class="small-12 columns">
					<label>
						{{ Form::select('customer_id', $customer_options ) }}
					</label>
				</div>
			</div>
		</div>
		<div class="row">
		<!-- Form Actions -->
			<button type="submit" class="button success">OK</button>
		<!-- ./ form actions -->
		</div>
	{{Form::close()}}
@stop

@section('scripts')
	<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.css') }}">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

	<script>
		$(function() {
			$(function() {
				$.datepicker.regional['es'] = {
				closeText: 'Cerrar',
				prevText: '<Ant',
				nextText: 'Sig>',
				currentText: 'Hoy',
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
				dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
				dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
				dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
				weekHeader: 'Sm',
				dateFormat: 'yy-mm-dd',
				firstDay: 1,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ''
				};
				$.datepicker.setDefaults($.datepicker.regional['es']);
				$( "#start_date" ).datepicker({
					dateFormat: "yy-mm-dd",
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true
				});
				$( "#end_date" ).datepicker({
					dateFormat: "yy-mm-dd",
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true
				});
			});
		});
	</script>
@stop
