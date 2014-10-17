@extends('layouts.modal')
{{-- Content --}}
@section('content')
<hr>
	<ul class="pricing-table">
	  <li class="title">Información del Articulo: <?php echo $items->name; ?></li>
	  <li class="title">UPC/EAN/ISBN: <?php echo $items->item_number; ?></li>
	  <li class="description"><?php echo $items->description; ?></li>
	  <li class="bullet-item">Categoria: <?php echo $items->category; ?></li>
	  <li class="bullet-item">Cantidad Actual: <?php echo $item_quantities->quantity; ?></li>
	</ul>
<hr>
<h1>Moviemientos de Inventario:</h1>

<table role="grid">
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Empleado</th>
      <th>In/Out Qty</th>
      <th>Comentario</th>
    </tr>
  </thead>
  <tbody>
  	@foreach($inventory as $key)
    <tr>
      <td>{{$key['created_at']}}</td>
      <td>{{$key['username']}}</td>
      <td>{{$key['inventory']}}</td>
      <td>{{$key['comment']}}</td>
    </tr>
	@endforeach
  </tbody>
</table>
<hr>
@stop