<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	ini_set('max_execution_time', 300);

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
	<title>Info Satifacción</title>
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
			<h1>Informe Encuesta Satisfacción</h1>
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
						<td  width="35%">Preguntas</td>
						<td width="6%">Completamente insatisfecho</td>
						<td width="6%">Algo insatisfecho</td>
						<td width="6%">Ni Insatisfecho Ni Satisfecho</td>
						<td width="6%">Algo Satisfecho</td>
						<td width="6%">Completamente Satisfecho</td>
						<td width="4%">Si</td>
						<td width="4%">No</td>
						<td class="col_csi" width="3%">CSI (ideal>=96%)</td>


					</tr>
				</thead>
				<tbody>

					<?php

						$val_1=0;
						$val_2=33;
						$val_3=67;
						$val_4=96;
						$val_5=100;
						$val_6=100;
						$val_7=0;

						$tot_col_1=0;
						$tot_col_2=0;
						$tot_col_3=0;
						$tot_col_4=0;
						$tot_col_5=0;
						$tot_col_6=0;
						$tot_col_7=0;

						$SQL="SELECT * FROM encuestas_preguntas WHERE id_encuesta=2 AND activo =1 AND calcula_info=1";
						$preguntas=mysqli_query($con, $SQL);
						$cont=0;
						while ($preg=mysqli_fetch_array($preguntas)) { $cont=$cont+1;

							$tot_linea=0;

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 3".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_1= $tot["cantidad"];

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 4".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_2= $tot["cantidad"];

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 5".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_3= $tot["cantidad"];

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 6".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_4= $tot["cantidad"];

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 7".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_5= $tot["cantidad"];

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 1".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_6= $tot["cantidad"];

							$SQL="SELECT sum(respuesta) as cantidad FROM view_cant_respuesta_satisfaccion wHERE id_pregunta = ".$preg["id_pregunta"]." AND selec = 2".$cad;
							$totales=mysqli_query($con, $SQL);
							$tot=mysqli_fetch_array($totales);
							$col_7= $tot["cantidad"];

							?>
						<tr>
							<td class="item_preg"><?php echo $cont."-".$preg["pregunta"]; ?></td>
							<td><div class="centrar-texto"><?php if ($col_1<>'') {echo $col_1; $tot_col_1 = $tot_col_1+$col_1;}else{echo '-';} ?></div></td>
							<td><div class="centrar-texto"><?php if ($col_2<>'') {echo $col_2; $tot_col_2 = $tot_col_2+$col_2;}else{echo '-';} ?></div></td>
							<td><div class="centrar-texto"><?php if ($col_3<>'') {echo $col_3; $tot_col_3 = $tot_col_3+$col_3;}else{echo '-';} ?></div></td>
							<td><div class="centrar-texto"><?php if ($col_4<>'') {echo $col_4; $tot_col_4 = $tot_col_4+$col_4;}else{echo '-';} ?></div></td>
							<td><div class="centrar-texto"><?php if ($col_5<>'') {echo $col_5; $tot_col_5 = $tot_col_5+$col_5;}else{echo '-';} ?></div></td>
							<td><div class="centrar-texto"><?php if ($col_6<>'') {echo $col_6; $tot_col_6 = $tot_col_6+$col_6;}else{echo '-';} ?></div></td>
							<td><div class="centrar-texto"><?php if ($col_7<>'') {echo $col_7; $tot_col_7 = $tot_col_7+$col_7;}else{echo '-';} ?></div></td>
							<td class="columna_resultado">
									<?php
										$cant_resp=$col_1+$col_2+$col_3+$col_4+$col_5+$col_6+$col_7;
										if ($cant_resp!=0) {
											$cal=round((($col_1*$val_1)+($col_2*$val_2)+($col_3*$val_3)+($col_4*$val_4)+($col_5*$val_5)+($col_6*$val_6)+($col_7*$val_7))/$cant_resp,2);
										}else{
											$cal=0;
										}

									?>
								<div class="<?php if ($cal<96) { echo 'espacio derecha-texto desaprobado';}else{ echo 'derecha-texto aprobado';} ?>">
									<?php
										echo $cal."  %";

									 ?>
									 <span class="<?php if ($cal<96) { echo 'icon-abajo espacio derecha-texto desaprobado';}else{ echo 'icon-arriba derecha-texto aprobado';} ?>"></span>

								</div>
							</td>
						</tr>
						<?php }  ?>
						<tr class="fila_resultado">
							<td colspan="8" class="item_preg">RESULTADO CSI</td>



							<?php
								$cant_resp=$tot_col_1+$tot_col_2+$tot_col_3+$tot_col_4+$tot_col_5+$tot_col_6+$tot_col_7;
								if ($cant_resp!=0) {
									$cal=round((($tot_col_1*$val_1)+($tot_col_2*$val_2)+($tot_col_3*$val_3)+($tot_col_4*$val_4)+($tot_col_5*$val_5)+($tot_col_6*$val_6)+($tot_col_7*$val_7))/$cant_resp,2);
								}else{
									$cal=0;
								}

							?>
							<td class="<?php if ($cal<96) { echo 'fila_resultado resultado_final_nook';}else{ echo 'fila_resultado resultado_final_ok';} ?>">

								<div class="<?php if ($cal<96) { echo 'espacio derecha-texto desaprobado ';}else{ echo 'derecha-texto aprobado ';} ?>">
									<?php
										echo $cal."  %";

									 ?>
									 <span class="<?php if ($cal<96) { echo 'icon-abajo espacio derecha-texto desaprobado ';}else{ echo 'icon-arriba derecha-texto aprobado ';} ?>"></span>

								</div>
							</td>
						</tr>

				</tbody>

			</table>
		</div>

		<?php
		$tot_gral = $tot_col_1 +  $tot_col_2 + $tot_col_3 + $tot_col_4 + $tot_col_5;
		$porc_1=  number_format(($tot_col_1 * 100 / $tot_gral),2);
		$porc_2=  number_format(($tot_col_2 * 100 / $tot_gral),2);
		$porc_3=  number_format(($tot_col_3 * 100 / $tot_gral),2);
		$porc_4=  number_format(($tot_col_4 * 100 / $tot_gral),2);
		$porc_5=  number_format(($tot_col_5 * 100 / $tot_gral),2);
		$tot_gral_sino = $tot_col_6 + $tot_col_7;
		$porc_6=  number_format(($tot_col_6 * 100 / $tot_gral_sino),2);
		$porc_7=  number_format(($tot_col_7 * 100 / $tot_gral_sino),2);

		 ?>

	</div>
	<div class="ed-container web-80">

		<div class="ed-item web-50">
			<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
		</div>
		<div class="ed-item web-50">
			<div id="container_dos" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
		</div>

	</div>
<script>
$(function () {
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Gráfico Representativo Según Calificación'
        },
        subtitle: {
            text: 'Calificación del 1 al 5'
        },
        xAxis: {
            categories: [
				<?php echo "'".$periodo."'"; ?>
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Porcentaje'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {

            column: {
		dataLabels: {
	                enabled: true
	            },
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Completamente insatisfecho',
            data: [<?php echo $porc_1; ?>]

        }, {
            name: 'Algo insatisfecho',
            data: [<?php echo $porc_2; ?>]

        }, {
            name: 'Ni Insatisfecho Ni Satisfecho',
            data: [<?php echo $porc_3; ?>]

        }, {
            name: 'Algo Satisfecho',
            data: [<?php echo $porc_4; ?>]

        }, {
            name: 'Completamente Satisfecho',
            data: [<?php echo $porc_5; ?>]

        }]
    });
    $('#container_dos').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Gráfico Representativo Según Realización'
        },
        subtitle: {
            text: 'Calificación Si - No'
        },
        xAxis: {
            categories: [
				<?php echo "'".$periodo."'"; ?>
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Porcentaje'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
		dataLabels: {
	                enabled: true
	            },
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Si',
            data: [<?php echo $porc_6; ?>]

        }, {
            name: 'No',
            data: [<?php echo $porc_7; ?>]

        }]
    });
});

</script>

</body>
</html>
