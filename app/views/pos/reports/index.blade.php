@extends('layouts.default')
{{-- Web site Title --}}
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

<div class="row">
  <div class=" large-12 columns">
    <ul class="large-block-grid-2">
      <li>
        <div class="panel"><h4>Reportes Gráficos</h4></div>
        <ul>
          <li>Ventas</li>
          <li>Categorí­as</li>
          <li>Clientes</li>
          <li>Proveedores</li>
          <li>Artículos</li>
          <li>Empleados</li>
          <li>Impuestos</li>
          <li>Descuentos</li>
          <li>Pagos</li>
        </ul>
      </li>
      <li>
        <div class="panel"><h4>Reportes Resumidos</h4></div>
        <ul>
          <li><a href="reports/summary_sales">Ventas</a></li>
          <li>Categorí­as</li>
          <li>Clientes</li>
          <li>Proveedores</li>
          <li>Artículos</li>
          <li>Empleados</li>
          <li>Impuestos</li>
          <li>Descuentos</li>
          <li>Pagos</li>
        </ul>
      </li>
      <hr>
      <li>
        <div class="panel"><h4>Reportes Detallados</h4></div>
        <ul>
          <li>Ventas</li>
          <li>Entradas</li>
          <li>Cliente</li>
          <li>Descuento mayor que</li>
          <li>Empleado</li>
        </ul>
      </li>
      <li>
        <div class="panel"><h4>Reportes de Inventario</h4></div>
        <ul>
          <li><a href="reports/asklowinventory">Inventario Bajo</a></li>
          <li><a href="reports/askinventory">Resumen de Inventario</a></li>
        </ul>
      </li>
    </ul>
  </div>
</div>

@stop
