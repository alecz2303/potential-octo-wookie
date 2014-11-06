@extends('layouts.default')

@include('num2letters.num2letter')


	@section('content')
	<div class="button" id="print_button"><i class="fa fa-print"></i> Imprimir Recibo</div>
	<div class="PrintArea">

		<div id="doc_header">
			<div id="store_name"><h2>{{$storeName->value}}</h2></div>
			<br />
			<div id="store_name"><i class="fa fa-calendar"></i> Fecha de Recepción: {{$sales->created_at->format('d-M-Y H:i')}}</div>
			<br />
		</div>
			<hr width="95%" align="center" size=1 noshade color = #000>
			<div id="client_info">
				<h3><i class="fa fa-briefcase"></i> DATOS DEL CLIENTE:</h3>
					@if($people!='Mostrador')
						<b><i class="fa fa-user"></i> {{$people->first_name.' '.$people->last_name}}</b><br>

						<i class="fa fa-home"></i> {{$people->address_1.', '.$people->address_2}}
						, <i class="fa fa-envelope-o"></i> C.P. {{$people->zip}} <br>
						<i class="fa fa-envelope-o"></i> {{$people->city.', '.$people->state.', '.$people->country}}
					@else
						<i><i class="fa fa-building-o"></i> Mostrador</i><br>
					@endif

			</div>
			<hr width="95%" align="center" size=1 noshade color = #000>


		<div class="receipt_name">
			<h3><i class="fa fa-shopping-cart"></i> Salida de Inventario</h3>
		</div>

		<div class="row">

		<table class="dataTable">
			<thead>
				<tr>
					<th>Cantidad</th>
					<th>Artículo</th>
					<th>Precio Unitario</th>
					<th>Importe</th>
				</tr>
			</thead>
			<tbody>
				<?php $total = 0; ?>
				@foreach ($sales_items as $key => $value)
					<tr>
						<td class="qty">{{$value->quantity_purchased}}</td>
						<td class="left">{{$value->name}}

							<br><i class="fa fa-pencil"></i> Descripción:
							<br>
								{{$value->description}}
							<br>
							<span>{{$value->serialnumber}}</span>
						</td>
						<td class="price">{{$value->item_unit_price}}</td>
						<td class="price"><i class="fa fa-usd"></i> {{number_format($value->item_unit_price * $value->quantity_purchased,2)}}</td>
					</tr>
					<?php $total += $value->item_unit_price * $value->quantity_purchased; ?>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td colspan=3>
						<h3><i class="fa fa-calculator"></i> SUB TOTAL:</h3>
					</td>
					<td>
						<h3><i class="fa fa-usd"></i> {{number_format($total,2)}}</h3>
					</td>
				</tr>
				<tr>
					<td colspan=3>
						<h3><i class="fa fa-calculator"></i> IMPUESTO:</h3>
					</td>
					<td>
						<h3><i class="fa fa-usd"></i> {{number_format($total,2)}}</h3>
					</td>
				</tr>
				<tr>
					<td colspan=3>
						<h3><i class="fa fa-calculator"></i> TOTAL:</h3>
					</td>
					<td>
						<h3><i class="fa fa-usd"></i> {{number_format($total,2)}}</h3>
					</td>
				</tr>
			</tfoot>
		</table>
		</div>

				<div class="container clearfix">

					<div class="row" align="center">
						<h3><i class="fa fa-calculator"></i> <i class="fa fa-pencil"></i> ({{num2letras(number_format($total, 2, '.', ''))}})</h3>
					</div>
						<hr width="95%" align="center" size=1 noshade color = #000>

				</div>

		<div id="doc_info">
		<!-- Display Notes -->

				<div>
					<h4><i class="fa fa-comments-o"></i> Comentarios</h4>
					<ul>
						<li>{{$sales->comment}}</li>
					</ul>
				</div>
		</div>

		    <div id="bcTarget" class="row"></div>
	</div>


@stop
	@section('scripts')
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">
		<script src="{{asset('js/jquery-barcode.js')}}"></script>
		<script>
			$("#bcTarget").barcode({code: "000{{$sales->id}}", crc:false}, "int25",{barWidth:2, barHeight:30});
		</script>
		 <script type="text/javascript" src="{{asset('js/jquery.print.js')}}"></script>
		<script src="{{asset('js/jquery.PrintArea.js')}}" type="text/JavaScript" language="javascript"></script>

		 <script type="text/javascript">
			$("div#print_button").click(function(){
            	$("div.PrintArea").printArea();
            });
		</script>
	@stop
