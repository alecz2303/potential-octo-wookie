<!DOCTYPE html>
<html>
<head>
    <title>Reporte Inventario</title>

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
	padding: 10px 15px;
	position: relative;
	transition: all 300ms;
}
</style>

</head>
<body>
    <h1>Reporte de Inventario Bajo</h1>

    <?php
        $items = Items::leftjoin('item_quantities', 'items.id', '=', 'item_quantities.item_id')
                    ->select(array('items.name', 'items.item_number', 'items.description','item_quantities.quantity', 'reorder_level'))
                    ->where('items.deleted','=','0')
                    ->get();
     ?>
    <table class="dataTable">
        <thead>
            <tr>
                <th>Nombre del Artículo</th>
                <th>UPC/EAN/ISBN</th>
                <th>Descripción</th>
                <th>Cuenta</th>
                <th>Cuenta Mínima</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $key => $value): ?>
                <tr>
                    <td>{{$value->name}}</td>
                    <td>{{$value->item_number}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->quantity}}</td>
                    <td>{{$value->reorder_level}}</td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</body>
</html>
