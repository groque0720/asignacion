<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	set_time_limit(300);

	extract($_GET);

		$cad='';

        $cad .=" AND YEAR(fecha_muestra_origen) = ".$año;
        $periodo = "Año ".$año;

		if ($mes!=0) {
			$cad .=" AND MONTH(fecha_muestra_origen) = ".$mes;
			$SQL="SELECT * FROM meses WHERE id_mes=".$mes;
			$meses=mysqli_fetch_array(mysql_query($SQL));
			$periodo=$meses["mmm"]." ".$año;
		}
		$evaluado = "Derka y Vargas S. A.";
		if ($suc!=0) {
			$cad .=" AND id_sucursal = ".$suc;
			$SQL = "SELECT * FROM sucursales WHERE id_sucursal=".$suc;
			$sucursal = mysqli_fetch_array(mysql_query($SQL));
			$evaluado = $sucursal["sucursal"];
		}

		if ($ase!=0) {
			$cad .=" AND id_asesor =".$ase;
			$SQL="SELECT * FROM usuarios WHERE id_usuario =".$ase;
			$asesor=mysqli_fetch_array(mysql_query($SQL));
			$evaluado = $asesor["nombre"];
		}	?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Reporte No Compra</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="../css/estilo_default.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="js_informe_panel.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="../js_highcharts/highcharts.js"></script>
	<script src="../js_highcharts/modules/exporting.js"></script>


</head>
<body class="desarroll">
	<div class="ed-container">
		-
	</div>
	<div class="ed-container web-80">
		<div class="ed-item web-1-3 movil-50">
			<img class="web-35 movil-30" src="../imagenes/logodyv.png" alt="logodyv">
		</div>
		<div class="ed-item web-1-3 movil-50 centrar-texto">
			<h1>Informe Encuesta No Compra</h1>
		</div>
		<div class="ed-item web-1-3 movil-50 derecha-contenido ">
			<img class="web-20 movil-10" src="../imagenes/logoect.png" alt="logodyv">
		</div>
		<div class="ed_item total">
		<hr>
		</div>

	</div>

	<div class="ed-container web-80">
		<div class="ed-item web-1-3 movil-1-3">
			<span>Cuadro de Resultados: <?php echo $evaluado; ?></span>
		</div>
		<div class="ed-item web-1-3 movil-1-3 centrar-texto ">
			<span>Período: <?php echo $periodo; ?></span>
		</div>
		<div class="ed-item web-1-3 movil-1-3 derecha-texto ">
			<span>Cantidad de Encuestas: <?php echo $cant; ?></span>
		</div>
		<div class="ed_item total">
		<hr>
		</div>
	</div>

	<div class="ed-container web-80">
		<div class="ed-item">
			<table class="tabla-default tabla-info">
				<thead>
					<tr>
						<td width="25%">Pregunta</td>
						<td colspan="3" width="25%">Cantidad</td>
						<td width="30%">Gráfico</td>
					</tr>
				</thead>
				<tbody>

					<?php
						$SQL="SELECT * FROM encuestas_preguntas WHERE id_encuesta=1 AND activo =1 AND calcula_info=1";
						$preguntas=mysqli_query($con, $SQL);
						$cont=0;
						while ($preg=mysqli_fetch_array($preguntas)) { $cont=$cont+1;

							$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE id_tipo_respuesta = ".$preg["id_tipo_respuesta"];
							$preguntas_lineas = mysqli_query($con, $SQL);
							$preguntas_lineas_uno = mysqli_query($con, $SQL);
							$cant_op=mysql_num_rows($preguntas_lineas);
					 ?>
						<tr class="fila_tabla">
							<td rowspan="<?php echo $cant_op+1; ?>"class="item_preg"><?php echo $cont."-".$preg["pregunta"]; ?></td>
							<td class="celda_sin_linea"></td>
							<td class="celda_sin_linea" width="3%"></td>
							<td class="celda_sin_linea" width="4%"></td>
							<!-- <td rowspan="<?php echo $cant_op+1; ?>">grafico</td> -->
							<td class="celda_sin_linea"</td>
						</tr>

						<?php
							$tot = 0;

						 while ($opcion = mysqli_fetch_array($preguntas_lineas_uno)) { ?>

								<?php
									$tot_g = 0;
									$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"].$cad;
									$totales=mysqli_query($con, $SQL);
									$tot=mysqli_fetch_array($totales);
									$tot_g = $tot_g + (int)$tot["cantidad"];
								?>

						<?php } ?>

						<?php
						$f = 0;
						while ($opcion = mysqli_fetch_array($preguntas_lineas)) { $f = $f +1;?>
								<?php
									$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = ".$opcion["id_linea_tipo_respuesta"].$cad;
									$totales=mysqli_query($con, $SQL);
									$tot=mysqli_fetch_array($totales);
									$result=(int)$tot["cantidad"];
									if ($result == 0) { ?>
										<tr class="fila_tabla fila_tabla_vacio"></tr>
									<?php }else{
								 ?>
							<tr class="">
								<td><?php echo $opcion["linea_tipo_respuesta"]; ?></td>

								<td><div class="centrar-texto"><?php echo $tot["cantidad"]; ?></div></td>
								<td><div class="derecha-texto"><?php if ($tot_g!=0) {	echo round((int)$tot["cantidad"]*100/$tot_g,2)." %"; $porc_graf=round((int)$tot["cantidad"]*100/$tot_g,2);}else{ echo 0.00." %";} ?></div></td>
								<td class=""><div class="<?php echo "centrar-texto grafico_linea color_".$f; ?>" style="<?php echo "width:".$porc_graf."%"; ?>" ><?php if ($porc_graf>=15) { echo "<span class='texto-dentro'>".$porc_graf."%</span>";} ?></div>
										<?php if ($porc_graf<15) { echo "<div class='texto-fuera'><span>".$porc_graf."%</span></div>";} ?>
								</td>
							</tr>

						<?php }} ?>


					<?php } ?>



				</tbody>

			</table>
		</div>

	</div>
	<div class="ed-container web-80">

		<div class="ed-item web-50">
			<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
		</div>
		<div class="ed-item web-50">
			<div id="container_dos" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
		</div>

	</div>


</body>
</html>
