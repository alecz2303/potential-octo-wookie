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
{{ Form::open(array('data-abide')) }}
  
  <!-- quantity -->
  <div class="row">
    <div class="large-3 columns">
      <label>Inventario a agregar o substraer: <small>Requerido</small>
        {{ Form::text('quantity', null,array('required')) }}
      </label>
      <small class="error">Cantidad es Requerido</small>
    </div>
  </div>
  <!-- quantity -->

  <!-- Comentarios -->
  <div class="row">
    <div class="large-9 columns">
    <label>Comentarios:
      {{ Form::textarea('comment', null,['rows'=>'2']) }}
    </label>
    </div>
  </div>
  <!-- Comentarios -->

  <div class="row">
  <!-- Form Actions -->
    <element class="button secondary close_popup">Cancelar</element>
    <button type="submit" class="button success">OK</button>
  <!-- ./ form actions -->
  </div>

{{ Form::close() }}
<hr>
@stop