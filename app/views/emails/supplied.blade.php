<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="font-family: 'Lato', sans-serif;">
<head style="font-family: 'Lato', sans-serif;">
<!-- If you delete this tag, the sky will fall on your head -->
<meta name="viewport" content="width=device-width" style="font-family: 'Lato', sans-serif;">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" style="font-family: 'Lato', sans-serif;">
<title style="font-family: 'Lato', sans-serif;">ZURBemails</title>
	
<style type="text/css" style="font-family: 'Lato', sans-serif;">
	/* ------------------------------------- 
		GLOBAL 
------------------------------------- */

@import url(http://fonts.googleapis.com/css?family=Dosis|Lato|Poiret+One);

* { 
	margin:0;
	padding:0;
}
* { font-family: 'Lato', sans-serif; }

img { 
	max-width: 100%; 
}
.collapse {
	margin:0;
	padding:0;
}
body {
	-webkit-font-smoothing:antialiased; 
	-webkit-text-size-adjust:none; 
	width: 100%!important; 
	height: 100%;
}


/* ------------------------------------- 
		ELEMENTS 
------------------------------------- */
a { color: #2BA6CB;}

.btn {
	text-decoration:none;
	color: #FFF;
	background-color: #666;
	padding:10px 16px;
	font-weight:bold;
	margin-right:10px;
	text-align:center;
	cursor:pointer;
	display: inline-block;
}

p.callout {
	padding:15px;
	background-color:#ECF8FF;
	margin-bottom: 15px;
}
.callout a {
	font-weight:bold;
	color: #2BA6CB;
}

table.social {
/* 	padding:15px; */
	background-color: #ebebeb;
	
}
.social .soc-btn {
	padding: 3px 7px;
	font-size:12px;
	margin-bottom:10px;
	text-decoration:none;
	color: #FFF;font-weight:bold;
	display:block;
	text-align:center;
}
a.fb { background-color: #3B5998!important; }
a.tw { background-color: #1daced!important; }
a.gp { background-color: #DB4A39!important; }
a.ms { background-color: #000!important; }

.sidebar .soc-btn { 
	display:block;
	width:100%;
}

/* ------------------------------------- 
		HEADER 
------------------------------------- */
table.head-wrap { width: 100%;}

.header.container table td.logo { padding: 15px; }
.header.container table td.label { padding: 15px; padding-left:0px;}


/* ------------------------------------- 
		BODY 
------------------------------------- */
table.body-wrap { width: 100%;}


/* ------------------------------------- 
		FOOTER 
------------------------------------- */
table.footer-wrap { width: 100%;	clear:both!important;
}
.footer-wrap .container td.content  p { border-top: 1px solid rgb(215,215,215); padding-top:15px;}
.footer-wrap .container td.content p {
	font-size:10px;
	font-weight: bold;
	
}


/* ------------------------------------- 
		TYPOGRAPHY 
------------------------------------- */
h1,h2,h3,h4,h5,h6 {
font-family: 'Lato', sans-serif; line-height: 1.1; margin-bottom:15px; color:#000;
}
h1 small, h2 small, h3 small, h4 small, h5 small, h6 small { font-size: 60%; color: #6f6f6f; line-height: 0; text-transform: none; }

h1 { font-weight:200; font-size: 44px;}
h2 { font-weight:200; font-size: 37px;}
h3 { font-weight:500; font-size: 27px;}
h4 { font-weight:500; font-size: 23px;}
h5 { font-weight:900; font-size: 17px;}
h6 { font-weight:900; font-size: 14px; text-transform: uppercase; color:#444;}

.collapse { margin:0!important;}

p, ul { 
	margin-bottom: 10px; 
	font-weight: normal; 
	font-size:14px; 
	line-height:1.6;
}
p.lead { font-size:17px; }
p.last { margin-bottom:0px;}

ul li {
	margin-left:5px;
	list-style-position: inside;
}

/* ------------------------------------- 
		SIDEBAR 
------------------------------------- */
ul.sidebar {
	background:#ebebeb;
	display:block;
	list-style-type: none;
}
ul.sidebar li { display: block; margin:0;}
ul.sidebar li a {
	text-decoration:none;
	color: #666;
	padding:10px 16px;
/* 	font-weight:bold; */
	margin-right:10px;
/* 	text-align:center; */
	cursor:pointer;
	border-bottom: 1px solid #777777;
	border-top: 1px solid #FFFFFF;
	display:block;
	margin:0;
}
ul.sidebar li a.last { border-bottom-width:0px;}
ul.sidebar li a h1,ul.sidebar li a h2,ul.sidebar li a h3,ul.sidebar li a h4,ul.sidebar li a h5,ul.sidebar li a h6,ul.sidebar li a p { margin-bottom:0!important;}



/* --------------------------------------------------- 
		RESPONSIVENESS
		Nuke it from orbit. It's the only way to be sure. 
------------------------------------------------------ */

/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
.container {
	display:block!important;
	max-width:600px!important;
	margin:0 auto!important; /* makes it centered */
	clear:both!important;
}

/* This should also be a block element, so that it will fill 100% of the .container */
.content {
	padding:15px;
	max-width:600px;
	margin:0 auto;
	display:block; 
}

/* Let's make sure tables in the content area are 100% wide */
.content table { width: 100%; }


/* Odds and ends */
.column {
	width: 300px;
	float:left;
}
.column tr td { padding: 15px; }
.column-wrap { 
	padding:0!important; 
	margin:0 auto; 
	max-width:600px!important;
}
.column table { width:100%;}
.social .column {
	width: 280px;
	min-width: 279px;
	float:left;
}

/* Be sure to place a .clear element after each set of columns, just to be safe */
.clear { display: block; clear: both; }


/* ------------------------------------------- 
		PHONE
		For clients that support media queries.
		Nothing fancy. 
-------------------------------------------- */
@media only screen and (max-width: 600px) {
	
	a[class="btn"] { display:block!important; margin-bottom:10px!important; background-image:none!important; margin-right:0!important;}

	div[class="column"] { width: auto!important; float:none!important;}
	
	table.social div[class="column"] {
		width:auto!important;
	}

}
	
</style>

</head>
 
<body bgcolor="#FFFFFF" style="font-family: 'Lato', sans-serif;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;height: 100%;width: 100%!important;">

<!-- HEADER -->
<table class="head-wrap" bgcolor="#999999" style="font-family: 'Lato', sans-serif;width: 100%;">
	<tr style="font-family: 'Lato', sans-serif;">
		<td style="font-family: 'Lato', sans-serif;"></td>
		<td class="header container" style="font-family: 'Lato', sans-serif;display: block!important;max-width: 600px!important;margin: 0 auto!important;clear: both!important;">
			
				<div class="content" style="font-family: 'Lato', sans-serif;padding: 15px;max-width: 600px;margin: 0 auto;display: block;">
				</div>
				
		</td>
		<td style="font-family: 'Lato', sans-serif;"></td>
	</tr>
</table><!-- /HEADER -->


<!-- BODY -->
<table class="body-wrap" style="font-family: 'Lato', sans-serif;width: 100%;">
	<tr style="font-family: 'Lato', sans-serif;">
		<td style="font-family: 'Lato', sans-serif;"></td>
		<td class="container" bgcolor="#FFFFFF" style="font-family: 'Lato', sans-serif;display: block!important;max-width: 600px!important;margin: 0 auto!important;clear: both!important;">

			<div class="content" style="font-family: 'Lato', sans-serif;padding: 15px;max-width: 600px;margin: 0 auto;display: block;">
			<table style="font-family: 'Lato', sans-serif;width: 100%;">
				<tr style="font-family: 'Lato', sans-serif;">
					<td style="font-family: 'Lato', sans-serif;">
						
						<h3 style="font-family: 'Lato', sans-serif;line-height: 1.1;margin-bottom: 15px;color: #000;font-weight: 500;font-size: 27px;">Hola, <strong style="font-family: 'Lato', sans-serif;">{{ $name }}</strong></h3>
						<p class="lead" style="font-family: 'Lato', sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 17px;line-height: 1.6;"><strong style="font-family: 'Lato', sans-serif;">{{ $company }}</strong> agradece tu preferencia.</p>
						<p class="lead" style="font-family: 'Lato', sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 17px;line-height: 1.6;">Hemos procesado tu pedido y pronto lo tendras en tus manos.</p>
						
						<!-- A Real Hero (and a real human being) -->
						<p style="font-family: 'Lato', sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 14px;line-height: 1.6;"><img src="http://i1374.photobucket.com/albums/ag408/Alecz_Rueda/ColourQuad-AutumnLeaves_zps533c2987.jpg" width="600" height="300" style="font-family: 'Lato', sans-serif;max-width: 100%;"></p><!-- /hero -->
						
						<p class="callout" style="font-family: 'Lato', sans-serif;margin-bottom: 15px;font-weight: normal;font-size: 14px;line-height: 1.6;padding: 15px;background-color: #ECF8FF;">El monto de este pedido es de <strong style="font-family: 'Lato', sans-serif;">$ {{ number_format($total_pedido,2) }}</strong>.</p>
						<p class="lead" style="font-family: 'Lato', sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 17px;line-height: 1.6;"><strong style="font-family: 'Lato', sans-serif;">GRACIAS POR TU PEDIDO.</strong></p>

						<br style="font-family: 'Lato', sans-serif;">
						<br style="font-family: 'Lato', sans-serif;">							
												
						<!-- social & contact -->
						<table class="social" width="100%" style="font-family: 'Lato', sans-serif;background-color: #ebebeb;width: 100%;">
							<tr style="font-family: 'Lato', sans-serif;">
								<td style="font-family: 'Lato', sans-serif;">
									
									
									<!--- column 2 -->
									<table align="left" class="column" style="font-family: 'Lato', sans-serif;width: 280px;float: left;min-width: 279px;">
										<tr style="font-family: 'Lato', sans-serif;">
											<td style="font-family: 'Lato', sans-serif;padding: 15px;">				
																			
												<h5 class="" style="font-family: 'Lato', sans-serif;line-height: 1.1;margin-bottom: 15px;color: #000;font-weight: 900;font-size: 17px;">Información de Contacto:</h5>												
												<p style="font-family: 'Lato', sans-serif;margin-bottom: 10px;font-weight: normal;font-size: 14px;line-height: 1.6;">Teléfono: <strong style="font-family: 'Lato', sans-serif;">{{ $company_phone }}</strong><br style="font-family: 'Lato', sans-serif;">
                Email: <strong style="font-family: 'Lato', sans-serif;"><a href="emailto:{{ $company_email }}" style="font-family: 'Lato', sans-serif;color: #2BA6CB;">{{ $company_email }}</a></strong></p>
                
											</td>
										</tr>
									</table><!-- /column 2 -->
									
									<span class="clear" style="font-family: 'Lato', sans-serif;display: block;clear: both;"></span>	
									
								</td>
							</tr>
						</table><!-- /social & contact -->
					
					
					</td>
				</tr>
			</table>
			</div>
									
		</td>
		<td style="font-family: 'Lato', sans-serif;"></td>
	</tr>
</table><!-- /BODY -->

</body>
</html>