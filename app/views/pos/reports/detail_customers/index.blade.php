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
		<h4>Rango de Fechas</h4>
		<div class="row panel">
			<div class="large-6 columns ">
				<div class="switch radius">
					<input id="exampleRadioSwitch1" type="radio" checked name="option" value="1">
					<label for="exampleRadioSwitch1"></label>
				</div>
				<div class="small-6 columns">
					<label>
						{{ Form::select(
							'date_range',
							array(
								date("'Y-m-d'")." and ".date("'Y-m-d 23:59:59'") =>'Hoy',
								date("'Y-m-d'",strtotime("-1 days"))." and ".date("'Y-m-d 23:59:59'",strtotime("-1 days")) =>'Ayer',
								date("'Y-m-d'",strtotime("-6 days"))." and ".date("'Y-m-d 23:59:59'") =>'Últimos 7 días',
								date("'Y-m-01'")." and ".date("'Y-m-d 23:59:59'", strtotime('last day of this month')) =>'Este Mes',
								date("'Y-m-d'", mktime(0, 0, 0, date("m")-1, 1, date("Y")))." and ".date("'Y-m-d 23:59:59'", mktime(0, 0, 0, date("m"), 0, date("Y"))) =>'Mes Pasado',
								date("'Y-01-01'")." and ".date("'Y-12-31 23:59:59'") =>'Este Año',
								date("'Y-m-d'", mktime(0, 0, 0, 1, 1, date("Y")-1))." and ".date("'Y-m-d 23:59:59'", mktime(0, 0, 0, 12, 31, date("Y")-1)) =>'Año Pasado',
								date("'1978-03-23'")." and ".date("'Y-m-d 23:59:59'") =>'Todos'
							),
							null
							)
						}}
					</label>
				</div>
			</div>
			<div class="large-6 columns ">
				<div class="switch radius">
					<input id="exampleRadioSwitch2" type="radio" name="option" value="2">
					<label for="exampleRadioSwitch2"></label>
				</div>
				<div class="small-6 columns">
					<label>
						{{Form::text('start_date',null,['id'=>'start_date'])}}
					</label>
				</div>
				<div class="small-6 columns">
					<label>
						{{Form::text('end_date',null,['id'=>'end_date'])}}
					</label>
				</div>
			</div>
		</div>
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
		<h4>Tipo de Venta</h4>
		<div class="row">
			<div class="large-6 columns panel">
				<div class="small-6 columns">
					<label>
						{{ Form::select(
							'sale_type',
							array(
								0 =>'Todo',
								1 =>'Ventas',
								2 =>'Devoluciones'
							),
							null
							)
						}}
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
	 <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
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
