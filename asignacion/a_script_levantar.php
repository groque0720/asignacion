<?php
	include("funciones/func_mysql.php");
	conectar();
	//mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$nro_dia=jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m"),date("d"), date("Y")) , 0 );

	echo $nro_dia.' borrado las modificaciones';

	$SQL="DELETE FROM a_modificaciones WHERE fecha < CURDATE()";
	mysqli_query($con, $SQL);


		$SQL="INSERT INTO sesiones (id_usuario, nombre, fecha, hora, latitud, longitud, ip) VALUES (1,'pepe','".date("Y-m-d")."','".date( 'H:i:s')."','','',5)";
	mysqli_query($con, $SQL);

	if ($nro_dia!=0) {

		$hora12 = strtotime( "11:59" );
		$hora22 = strtotime( "21:59" );
		$hora_actual = time();

		echo $hora12. " - ".$hora22." - ".$hora_actual." +++ ";

		$SQL="SELECT * FROM feriados WHERE dia = CURDATE()";
		$feriados=mysqli_query($con, $SQL);
		$cant_feriados = mysqli_num_rows($feriados);

		if ($cant_feriados<1) {

			if ($hora_actual>$hora12) { //

				echo "12 medio dia <br><br><br>";

				$SQL="SELECT * FROM view_asignaciones_no_confirmada WHERE (fec_reserva < CURDATE() AND hora < '22:00') OR (fec_reserva < CURDATE()-1)";
				$unidades=mysqli_query($con, $SQL);

				while ($unidad=mysqli_fetch_array($unidades)) {

					$SQL="INSERT INTO a_modificaciones (modelo_activo, fecha) VALUES(".$unidad['id_modelo'].",'".date("Y-m-d")."')";
					mysqli_query($con, $SQL);

					$SQL="INSERT INTO asignaciones_levantadas (nro_unidad, id_mes, año, id_grupo, id_modelo, fec_reserva, fec_alta, hora, cliente, id_asesor, hora_alta) VALUES (".$unidad['nro_unidad'].", ".$unidad['id_mes'].", ".$unidad['año'].", ".$unidad['id_grupo'].", ".$unidad['id_modelo'].", '".$unidad['fec_reserva']."', '".date("Y-m-d")."', '".$unidad['hora']."', '".$unidad['cliente']."', ".$unidad['id_asesor'].", '".date( 'H:i:s')."')";
					mysqli_query($con, $SQL);
					echo $unidad['cliente'];
					$SQL="UPDATE asignaciones SET ";
					$SQL.=" fec_reserva = null, ";
					$SQL.=" hora = null, ";
					$SQL.=" cliente = '', ";
					$SQL.=" id_asesor = 1, ";
					$SQL.=" fec_limite = null, ";

					if ($unidad['color_uno'] != 23){
					$SQL.=" color_uno = 0, ";
					}

					$SQL.=" color_dos = 0, ";
					$SQL.=" color_tres = 0, ";
					$SQL.=" reservada = 0, ";
					$SQL.=" id_sucursal = null ";
					$SQL .=" WHERE id_unidad = ".$unidad['id_unidad'];
					mysqli_query($con,$SQL);
				}
				echo 'Usados';
				//levantar usados
				//
				$SQL="SELECT * FROM view_asignaciones_usados_no_confirmados";
				$usados=mysqli_query($con, $SQL);

				while ($usado = mysqli_fetch_array($usados)) {

					echo $usado['nro_unidad'].'-'.$usado['cliente'];

					$SQL="INSERT INTO asignaciones_usados_levantadas (nro_unidad, id_estado, interno, vehiculo, cliente, id_asesor, fec_reserva, hora, fec_alta, hora_alta, asesor) VALUES (".$usado['nro_unidad'].", ".$usado['id_estado'].", ".$usado['interno'].", '".$usado['vehiculo']."', '".$usado['cliente']."', ".$usado['id_asesor'].", '".$usado['fec_reserva']."', '".$usado['hora']."', '".date("Y-m-d")."','".date( 'H:i:s')."', '".$usado['asesor']."')";
					mysqli_query($con,$SQL);

					$SQL="UPDATE asignaciones_usados SET ";
					$SQL.=" cliente = '', ";
					$SQL.=" id_asesor = 1, ";
					$SQL.=" fec_reserva = null, ";
					$SQL.=" hora = null, ";
					$SQL.=" reservada = 0 ";
					$SQL .=" WHERE id_unidad = ".$usado['id_unidad'];
					mysqli_query($con,$SQL);
				}

				//fin levantar usados

			}// fin If 12:00

				if ($nro_dia!=6 AND $nro_dia!=0) {

						if ($hora_actual>$hora22) {

							echo "22 medio dia <br><br><br>";

							$SQL="SELECT * FROM view_asignaciones_no_confirmada WHERE (fec_reserva = CURDATE() AND hora < '12:00') OR (fec_reserva < CURDATE())";
							$unidades=mysqli_query($con, $SQL);

							while ($unidad=mysqli_fetch_array($unidades)) {

								$SQL="INSERT INTO a_modificaciones (modelo_activo, fecha) VALUES(".$unidad['id_modelo'].",'".date("Y-m-d")."')";
								mysqli_query($con, $SQL);

								$SQL="INSERT INTO asignaciones_levantadas (nro_unidad, id_mes, año, id_grupo, id_modelo, fec_reserva, fec_alta, hora, cliente, id_asesor, hora_alta) VALUES (".$unidad['nro_unidad'].", ".$unidad['id_mes'].", ".$unidad['año'].", ".$unidad['id_grupo'].", ".$unidad['id_modelo'].", '".$unidad['fec_reserva']."', '".date("Y-m-d")."', '".$unidad['hora']."', '".$unidad['cliente']."', ".$unidad['id_asesor'].", '".date( 'H:i:s')."')";
								mysqli_query($con, $SQL);
								echo $unidad['cliente'];
								$SQL="UPDATE asignaciones SET ";
								$SQL.=" fec_reserva = null, ";
								$SQL.=" hora = null, ";
								$SQL.=" cliente = '', ";
								$SQL.=" id_asesor = 1, ";
								$SQL.=" id_asesor = 1, ";
								$SQL.=" fec_limite = null, ";

								if ($unidad['color_uno'] != 23){
									$SQL.=" color_uno = 0, ";
								}

								$SQL.=" color_dos = 0, ";
								$SQL.=" color_tres = 0, ";
								$SQL.=" reservada = 0, ";
								$SQL.=" id_sucursal = null ";
								$SQL .=" WHERE id_unidad = ".$unidad['id_unidad'];
								mysqli_query($con,$SQL);

							}

						}

				}	// fin if 22:00
	}
}



?>


