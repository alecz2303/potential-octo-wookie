@extends('layouts.default')
{{-- Web site Title --}}
@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
<div class="row">
  <div class="large-12 columns">
      <h2>{{$title}}</h2>
  </div>
</div>


<dl class="tabs" data-tab>
  <dd class="active"><a href="#panel1">Reportes Gráficos</a></dd>
  <dd><a href="#panel2">Reportes Resumidos</a></dd>
  <dd><a href="#panel3">Reportes Detallados</a></dd>
  <dd><a href="#panel4">Reportes de Inventario</a></dd>
</dl>
<hr>
<div class="tabs-content">
  <div class="content active" id="panel1">
    <div class="panel"><h4>Reportes Gráficos</h4></div>
    <ul class="circle">
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
  </div>
  <div class="content" id="panel2">
    <div class="panel"><h4>Reportes Resumidos</h4></div>
    <ul class="circle">
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
  </div>
  <div class="content" id="panel3">
    <div class="panel"><h4>Reportes Detallados</h4></div>
    <ul class="circle">
      <li><a href="reports/detail_sales">Ventas</a></li>
      <li><a href="reports/detail_receivings">Entradas</a></li>
      <li><a href="reports/detail_customers">Cliente</a></li>
      <li><a href="reports/detail_users">Empleado</a></li>
    </ul>
  </div>
  <div class="content" id="panel4">
    <div class="panel"><h4>Reportes de Inventario</h4></div>
    <ul class="circle">
      <li><a href="reports/low">Inventario Bajo</a></li>
      <li><a href="reports/inventory">Resumen de Inventario</a></li>
    </ul>
  </div>
</div>
@stop
