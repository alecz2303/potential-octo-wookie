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
          <li><a href="reports/graphic/sales">Ventas</a></li>
          <li><a href="reports/graphic/category">Categorí­as</a></li>
          <li><a href="reports/graphic/customer">Clientes</a></li>
          <li><a href="reports/graphic/supplier">Proveedores</a></li>
          <li><a href="reports/graphic/item">Artículos</a></li>
          <li><a href="reports/graphic/user">Empleados</a></li>
          <li><a href="reports/graphic/tax">Impuestos</a></li>
          <li><a href="reports/graphic/discount">Descuentos</a></li>
          <li><a href="reports/graphic/payment">Pagos</a></li>
        </ul>
      </li>
      <li>
        <div class="panel"><h4>Reportes Resumidos</h4></div>
        <ul>
          <li><a href="reports/summary_sales">Ventas</a></li>
          <li><a href="reports/summary_categories">Categorí­as</a></li>
          <li><a href="reports/summary_customers">Clientes</a></li>
          <li><a href="reports/summary_suppliers">Proveedores</a></li>
          <li><a href="reports/summary_items">Artículos</a></li>
          <li><a href="reports/summary_users">Empleados</a></li>
          <li><a href="reports/summary_taxes">Impuestos</a></li>
          <li><a href="reports/summary_discounts">Descuentos</a></li>
          <li><a href="reports/summary_payments">Pagos</a></li>
        </ul>
      </li>
      <hr>
      <li>
        <div class="panel"><h4>Reportes Detallados</h4></div>
        <ul>
          <li><a href="reports/detail_sales">Ventas</a></li>
          <li><a href="reports/detail_receivings">Entradas</a></li>
          <li><a href="reports/detail_customers">Cliente</a></li>
          <li><a href="reports/detail_users">Empleado</a></li>
        </ul>
      </li>
      <li>
        <div class="panel"><h4>Reportes de Inventario</h4></div>
        <ul>
          <li><a href="reports/low">Inventario Bajo</a></li>
          <li><a href="reports/inventory">Resumen de Inventario</a></li>
        </ul>
      </li>
      <hr>
    </ul>
  </div>
</div>

@stop
