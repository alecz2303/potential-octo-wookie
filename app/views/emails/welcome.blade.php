<!DOCTYPE html>
<html>
<body>
	<h1>Hola {{ $name_company }}</h1>
	<p>Has recibido un nuevo pedido de:</p>
	<hr>
	<div style="width: 100%; border-radius: 10px 10px 10px 10px; -moz-border-radius: 10px 10px 10px 10px; -webkit-border-radius: 10px 10px 10px 10px; border: 0px solid #000000; -webkit-box-shadow: -14px 13px 7px  0px rgba(0,0,0,0.75); -moz-box-shadow: -14px 13px 7px 0px rgba(0,0,0,0.75); box-shadow: -14px 13px 7px 0px rgba(0,0,0,0.75); padding: 10px; border: 1px solid #000000;">
		<table rules="all" style="border-color: #666; width: auto;" cellpadding="10">
			<thead>
				<tr style='background: #eee;'>
					<th>Nombre</th>
					<th>Correo</th>
					<th>Teléfono</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{ $nombre.' '.$ap_pat.' '.$ap_mat }}</td>
					<td>{{ $email }}</td>
					<td>{{ $phone }}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<hr>
	<h3>Revisa tu listado de pedidos pendientes.</h3>
</body>
</html>