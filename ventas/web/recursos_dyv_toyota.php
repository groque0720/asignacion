<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Control de Pagos - Derka y Vargas</title>
<link rel="stylesheet" href="../css/normalize.css">
<link rel="stylesheet" href="../css/pagos.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){

	$("#carga").hide();

	$("#informe").change(function(){

		if ($("#informe").val()==2) {

			
			$("#carga").show();
			var myDate = new Date();
			var displayDate = (myDate.getDate()) + '-' + (myDate.getMonth()+1) + '-' + myDate.getFullYear();
			info=$("#informe option:selected").text();
			leyenda = "<h1> Informe Segun : <strong style='font-style:italic;'>"+info+"</strong> - Fecha: <strong style='font-style: italic;'>"+displayDate+"</strong> </h1> ";
			$("#titulo").html(leyenda);

			idinfo=$("#informe").val();
			
			$.ajax({url:"estado_unidad_filtro.php",cache:false,type:"POST",data:{id:idinfo},success:function(result){
		      	$("#actualizar").html(result);
		      	$("#carga").hide();

		    	}});
		};

		if ($("#informe").val()==1) {
			window.open('../../asignacion/costos_TASA_pdf.php','_blank');
		}

		if ($("#informe").val()==3) {
			window.open('../../asignacion/planilla_asignacion_stock_pdf.php','_blank');
		}


	});
});
</script>

</head>
<body>

	<header>
		
		<?php @session_start();

		//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
		if ($_SESSION["autentificado"] != "SI") {
			//si no existe, envio a la página de autentificacion
			header("Location: ../index.php");
			//ademas salgo de este script
			exit();
		}?>

	<input type="hidden" id="perfil" value="<?php echo $_SESSION["idperfil"]; ?>">
		<div id="titulo">
			<h1>Control de Pagos - Derka y Vargas S. A. - Fecha: <?php echo date('d-m-Y'); ?></h1>
		</div>
		
	</header>
	<nav >
		<ul class="menu">
			<li>
				<label for="">Informe Seg&uacute;n:</label>
				<select name="informe" id="informe">
					<option value="0"></option>
					<option value="1">Costos TASA</option>
					<option value="2">Recurso Cliente</option>
					<option value="3">Stock Asignación</option>
					<!-- <option value="3">Fecha de Arribo</option> -->
					<!-- <option value="4">Operaciones Activas (Por fecha de Arribo)</option> -->
					
				</select>
			</li>
			<li>
				<div id="carga">
					<img src="../imagenes/carga.gif" alt="Cargando">
				</div>
			</li>
			
			
		</ul>
<!-- 		<div class="busqueda">
			<input type="text" id="texto_busqueda" name="texto_busqueda">
			<a href="#" class="buscar" id="buscar">Buscar</a>
		</div> -->
	</nav>

	<section>


			<article class="tabla">

				<div id="actualizar">
				
				

				</div>
			</article>
	</section>

	
	
</body>

</html>