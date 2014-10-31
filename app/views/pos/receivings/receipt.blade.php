<html>
	<head>
		<title></title>
	</head>
	<body>
		Impresion de Recibo

		<?php
			print_r($receivings);
		?>

		    <div id="bcTarget"></div>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<script type="text/javascript" src="{{asset('js/jquery-barcode.js')}}"></script>

		<script>
			$("#bcTarget").barcode({code: "000{{$receivings->id}}", crc:false}, "int25",{barWidth:2, barHeight:30});
		</script>
	</body>
</html>
