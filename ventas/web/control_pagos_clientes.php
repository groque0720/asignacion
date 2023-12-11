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

	var refreshId = setInterval(refrescarTablaEstadoSala, 8000);
	$.ajaxSetup({ cache: false });

	function refrescarTablaEstadoSala() {
	id=$("#idusu").val();
	$("#noti").load('control_pagos_cliente_noti.php?id='+id, function(){});
	};

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
	<input type="hidden" id="idusu" value="<?php echo $_SESSION["id"]; ?>">
		<div id="titulo">
			<h1>Control de Pagos - Derka y Vargas S. A. - Fecha: <?php echo date('d-m-Y'); ?></h1>

		</div>

	</header>


		<div id="noti" style="float:right; background:Red;  padding:5px;">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				mysqli_query($con, "SET NAMES 'utf8'");
				$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_SESSION["id"]." AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{
					 $cant_res=mysqli_fetch_array($res);
					if ($cant_res['cantidad']>0) { ?>
					<a href="noticias.php" style="text-decoration:none;color:white;"  target="_blank">Notificaciones Nuevas:
					<?php echo $cant_res['cantidad'];}
				}
					mysqli_close($con);
			?>
				</a>
		</div>
		<div id="noti" style="float:left; background:green;  padding:5px;">
			<a href="noticias.php" style="text-decoration:none;color:white;"  target="_blank">Ver Notificaciones</a>
		</div>

	<nav >
		<ul class="menu">
			<li>
				<label for="">Sucursal:</label>
				<select name="sucursal" id="sucursal">
					<option value="0">Todas</option>
					<option value="1">Resistencia</option>
					<option value="2">S&aacute;enz Peña</option>
					<option value="3">Villa Angela</option>
					<option value="4">Charata</option>
				</select>
			</li>

			<li>
				<label for="">Estado:</label>
				<select name="estado" id="estado">
					<option value="1">Llegadas Todas</option>
					<option value="11" selected>Llegadas No Canceladas</option>
					<option value="12">Llegadas Canceladas</option>
					<option value="2">No Llegadas</option>
					<option value="21">No Llegadas Canceladas</option>
					<option value="3">Llegadas +10 d&iacute;as</option>
					<option value="4">Cancelación Vencida</option>
				</select>
			</li>


			<li>
				<label for="">Cr&eacute;ditos</label>
				<select name="creditos" id="creditos">
					<option value="0"></option>
					<option value="1">Estado Gral</option>
					<option value="2">Atrasados</option>
					<option value="3">A vencer</option>
				</select>
			</li>

			<li>
				<div id="carga">
					<img src="../imagenes/carga.gif" alt="Cargando">
				</div>
			</li>


		</ul>
		<div class="busqueda" style="width: 50%; display: flex; justify-content: space-between;">
			<div style="width: 35%;">
				<label>Venta:</label>
				<select id="tipo_venta" name="tipo_venta" required>
					<option value=""></option>
					<option value="Convencional">Convencional</option>
					<option value="Usado Certificado">Usado Certificado</option>
					<option value="Reventa">Reventa</option>
					<option value="Plan Dueño">Plan Dueño</option>
					<option value="Plan Empleado">Plan Empleado</option>
					<option value="Especial">Especial</option>
					<option value="Plan de Ahorro">Plan de Ahorro</option>
					<option value="Plan Adjudicado">Plan Adjudicado</option>
					<option value="Plan Avanzado">Plan Avanzado</option>
					<option value="Reg. Discapacidad">Reg. Discapacidad</option>
				</select>
			</div>
			<div style="width: 65%;">
				<input type="text" id="texto_busqueda" name="texto_busqueda">
				<a href="#" class="buscar" id="buscar">Buscar</a>
			</div>

		</div>
	</nav>

	<section>


			<article class="tabla">

				<div id="actualizar">

				<?php // include('control_pagos_cliente_cuerpon.php'); ?>

				</div>
			</article>
	</section>
	<div id="form" title="Datos de la Operación">
		<input type="hidden" id="nrofila" name="nrofila">
		<input type="hidden" id="idreserva" name="idreserva">
		<label for="nrounidad">Nro Unidad</label>
		<input type="number" id="nrounidad" name="nrounidad" placeholder="Nro Unidad" size="15" required="true">
		<hr>
		<label for="interno">Interno</label>
		<input type="text" id="interno" name="interno" placeholder="Interno" size="15">
		<hr>
		<label for="nroorden">Nro Orden</label>
		<input type="text" id="nroorden" name="nroorden" placeholder="Nro Orden" size="15">
		<hr>
		<label for="llego">Fec. de Arribo</label>
		<input type="date" id="arribo" name="arribo">
		<hr>
		<label for="cancela" > Fec. Estimada de Cancelaci&oacute;n</label>
		<input type="date" id="cancela" name="cancela">
		<hr>
		Observaci&oacute;n
		<textarea name="obs" id="obs" cols="46" rows="4"></textarea>
		<hr>
		<label for="entrega" >Fecha de Entrega</label>
		<input type="date" id="entrega" name="entrega">
	</div>
	<script>
	</script>
	<script src="../js/control_pagos.js"></script>
</body>

</html>