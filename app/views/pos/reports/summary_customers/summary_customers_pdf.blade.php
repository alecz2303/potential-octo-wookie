<!DOCTYPE html>
<html>
<head>
	<title>Reporte Clientes</title>

<style>

strong {
	font-weight: bold;
}

em {
	font-style: italic;
}

table {
	background: #fafafa;
	border-collapse: separate;
	box-shadow: inset 0 1px 0 #fff;
	font-size: 12px;
	line-height: 24px;
	margin: 30px auto;
	text-align: left;
	width: auto;
}

th {
	background:  #444;
	border-left: 1px solid #555;
	border-right: 1px solid #777;
	border-top: 1px solid #555;
	border-bottom: 1px solid #333;
	box-shadow: inset 0 1px 0 #999;
	color: #fff;
	font-weight: bold;
	padding: 10px 15px;
	position: relative;
	text-shadow: 0 1px 0 #000;
}


td {
	border-right: 1px solid #fff;
	border-left: 1px solid #e8e8e8;
	border-top: 1px solid #fff;
	border-bottom: 1px solid #e8e8e8;
	padding: 5px 15px;
	position: relative;
	transition: all 300ms;
	text-align: right;
}

/* Pricing Tables */
.pricing-table {
border: solid 1px #dddddd;
margin-left: 0;
margin-bottom: 1.25rem; }
.pricing-table * {
	list-style: none;
	line-height: 1; }
.pricing-table .title {
	background-color: #333333;
	padding: 0.9375rem 1.25rem;
	text-align: center;
	color: #eeeeee;
	font-weight: normal;
	font-size: 1rem;
	font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }
.pricing-table .price {
	background-color: #f6f6f6;
	padding: 0.9375rem 1.25rem;
	text-align: center;
	color: #333333;
	font-weight: normal;
	font-size: 2rem;
	font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; }
.pricing-table .description {
	background-color: white;
	padding: 0.9375rem;
	text-align: center;
	color: #777777;
	font-size: 0.75rem;
	font-weight: normal;
	line-height: 1.4;
	border-bottom: dotted 1px #dddddd; }
.pricing-table .bullet-item {
	background-color: white;
	padding: 0.9375rem;
	text-align: center;
	color: #333333;
	font-size: 0.875rem;
	font-weight: normal;
	border-bottom: dotted 1px #dddddd; }
.pricing-table .cta-button {
	background-color: white;
	text-align: center;
	padding: 1.25rem 1.25rem 0; }
</style>

</head>
<body>
	<h1>Reporte de Resumen de Clientes <small>{{$date_range}}</small></h1>

	<table class="dataTable">
		<thead>
			<tr>
				<th>Cliente</th>
				<th>Sub Total</th>
				<th>Impuesto</th>
				<th>Total</th>
				<th>Ganancia</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($sales as $key => $value)
				<tr>
					<td>{{$value->full_name}}</td>
					<td>{{number_format($value->subtotal,2)}}</td>
					<td>{{number_format($value->tax,2)}}</td>
					<td>{{number_format($value->total,2)}}</td>
					<td>{{number_format($value->ganancia,2)}}</td>
				</tr>
			@endforeach

		</tbody>
	</table>
<hr>
	<?php
		$subtotal = 0;
		$total = 0;
		$tax = 0;
		$ganancia = 0;
		foreach ($sales as $key => $value){
			$subtotal += $value->subtotal;
			$total += $value->total;
			$tax += $value->tax;
			$ganancia += $value->ganancia;
		}
	?>

	<ul class="pricing-table">
		<li class="title">Resumen Clientes</li>
		<li class="bullet-item">Sub Total: {{number_format($subtotal,2)}}</li>
		<li class="bullet-item">Total: {{number_format($total,2)}}</li>
		<li class="bullet-item">Impuesto: {{number_format($tax,2)}}</li>
		<li class="bullet-item">Ganancia: {{number_format($ganancia,2)}}</li>
	</ul>

</body>
</html>
