<?php
	set_time_limit(300);
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");

	$SQL="SELECT * FROM colores ORDER BY color";
	$colores=mysqli_query($con, $SQL);
	$color_a[]['color']= '-';
	$color_a[0]['color']= '-';
	$i=1;
	while ($color=mysqli_fetch_array($colores)) {
		$color_a[$color['idcolor']]['color']= $color['color'];
		$i++;
	}

	$SQL="SELECT * FROM modelos WHERE activo = 1";
	$modelos=mysqli_query($con, $SQL);

$total_asignaciones=0;

	while ($modelo=mysqli_fetch_array($modelos)) {//primer clico

		$cont_asignaciones=0;


		$SQL="SELECT * FROM view_asignaciones_asignar WHERE id_modelo = ".$modelo['idmodelo'];
		$unidades = mysqli_query($con, $SQL);

		while ($unidad=mysqli_fetch_array($unidades)) {

			$color_unidad[1]['color']=$unidad['color_uno'];
			$color_unidad[2]['color']=$unidad['color_dos'];
			$color_unidad[3]['color']=$unidad['color_tres'];
			$color_asignado=0;

			for ($i=1; $i < 4; $i++) {

				if ($color_asignado==0) {

					$SQL="SELECT * FROM hoja1 WHERE id_modelo = '".$unidad['id_modelo']."' AND id_color = '".$color_unidad[$i]['color']."'";
					$unidades_tasa=mysqli_query($con, $SQL);
					$cant_color=mysqli_num_rows($unidades_tasa);

					if ($cant_color==0) {
						echo $unidad['cliente']."<- SIN COLOR ASIGNADO <br>";

					}else{

						$unidad_tasa=mysqli_fetch_array($unidades_tasa);
						echo $unidad['cliente']."<-".$color_a[$unidad_tasa['id_color']]['color']."<br>";

						$fecha=$unidad_tasa["fec_playa"];
						list($dia, $mes, $año) = explode("/", $fecha);
						$fecha= strtotime($dia."-".$mes."-".$año);
						$fec_playa = date('Y-m-d', $fecha);

						$fecha=$unidad_tasa["fec_despacho"];
						list($dia, $mes, $año) = explode("/", $fecha);
						$fecha= strtotime($dia."-".$mes."-".$año);
						$fec_despacho = date('Y-m-d', $fecha);


						$SQL="UPDATE asignaciones SET fec_despacho = '".$fec_despacho."', fec_playa = '".$fec_playa."', id_color = ".$unidad_tasa['id_color'].", nro_orden =".$unidad_tasa['nro_orden']." WHERE id_unidad = ".$unidad['id_unidad'];
						mysqli_query($con, $SQL);


						$SQL="DELETE FROM hoja1 WHERE id =".$unidad_tasa['id'];
						mysqli_query($con, $SQL);


						$color_asignado=1;
						$cont_asignaciones++;
						$total_asignaciones++;

					}
				}
			}
		} //fin primer ciclo


		$SQL="SELECT * FROM view_asignaciones_asignar WHERE id_modelo = ".$modelo['idmodelo'];
		$unidades = mysqli_query($con, $SQL);

		while ($unidad=mysqli_fetch_array($unidades)) {

			$SQL="SELECT * FROM hoja1 WHERE id_modelo = '".$unidad['id_modelo']."'";
			$unidades_tasa=mysqli_query($con, $SQL);

			$unidad_tasa=mysqli_fetch_array($unidades_tasa);
			//echo $unidad['cliente']."<-".$color_a[$unidad_tasa['id_color']]['color']."<-".$unidad_tasa['id_color']."<br>";

			$fecha=$unidad_tasa["fec_playa"];

			echo $fecha;

		      list($dia, $mes, $año) = explode("/", $fecha);
		      $fecha= strtotime($dia."-".$mes."-".$año);
		      $fec_playa = date('Y-m-d', $fecha);

		      $fecha=$unidad_tasa["fec_despacho"];
		      list($dia, $mes, $año) = explode("/", $fecha);
		      $fecha= strtotime($dia."-".$mes."-".$año);
		      $fec_despacho = date('Y-m-d', $fecha);


			$SQL="DELETE FROM hoja1 WHERE id =".$unidad_tasa['id'];
			mysqli_query($con, $SQL);

			$SQL="UPDATE asignaciones SET fec_despacho = '".$fec_despacho."', fec_playa = '".$fec_playa."', id_color = ".$unidad_tasa['id_color'].", nro_orden =".$unidad_tasa['nro_orden']." WHERE id_unidad = ".$unidad['id_unidad'];
			mysqli_query($con, $SQL);

			$cont_asignaciones++;
			$total_asignaciones++;

		}

	echo $cont_asignaciones." ".$modelo['modelo']."<br><br>";

}

	echo 'Total de Asignadas '.$total_asignaciones;

?>


