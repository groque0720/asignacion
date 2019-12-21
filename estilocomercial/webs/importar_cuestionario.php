<?php

set_time_limit(300);

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

   extract($_POST);

        $archivo = $_FILES['excel']['name'];
        $tipo = $_FILES['excel']['type'];
        $destino = "bak_". $archivo;

        if (move_uploaded_file($_FILES['excel']['tmp_name'], $destino)) {
        	echo "exito";
        }else{
        	echo "no exito";
        }
        if (file_exists("bak_" . $archivo)) {
		echo "cargo";
            require_once('../Classes/PHPExcel.php');
            require_once('../Classes/PHPExcel/Reader/Excel2007.php');

            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = $objReader->load("bak_" . $archivo);
            $objFecha = new PHPExcel_Shared_Date();

            $objPHPExcel->setActiveSheetIndex(0);

            for ($i = 6; $i <= 100; $i++) {

            		$p=$i-5;
	                $_DATOS_EXCEL[$p]['id_encuesta'] = 2;
	                $_DATOS_EXCEL[$p]['fecha_origen'] = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['fecha_encuesta'] = $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['cliente'] = $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['profesion'] = $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['telefono'] = $objPHPExcel->getActiveSheet()->getCell('G' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['modelo_version'] = $objPHPExcel->getActiveSheet()->getCell('H' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['asesor'] = $objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_1'] = $objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_2'] = $objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_3'] = $objPHPExcel->getActiveSheet()->getCell('L' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_4'] = $objPHPExcel->getActiveSheet()->getCell('M' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_5'] = $objPHPExcel->getActiveSheet()->getCell('N' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_6'] = $objPHPExcel->getActiveSheet()->getCell('O' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_7'] = $objPHPExcel->getActiveSheet()->getCell('P' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_8'] = $objPHPExcel->getActiveSheet()->getCell('Q' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_9'] = $objPHPExcel->getActiveSheet()->getCell('R' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_10'] = $objPHPExcel->getActiveSheet()->getCell('S' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_11'] = $objPHPExcel->getActiveSheet()->getCell('T' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_12'] = $objPHPExcel->getActiveSheet()->getCell('U' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_13'] = $objPHPExcel->getActiveSheet()->getCell('V' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_14'] = $objPHPExcel->getActiveSheet()->getCell('W' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_15'] = $objPHPExcel->getActiveSheet()->getCell('X' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_16'] = $objPHPExcel->getActiveSheet()->getCell('Y' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_17'] = $objPHPExcel->getActiveSheet()->getCell('Z' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['preg_18'] = $objPHPExcel->getActiveSheet()->getCell('AA' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$p]['condicion'] = $objPHPExcel->getActiveSheet()->getCell('AB' . $i)->getCalculatedValue();
            }
	}else {
            echo "Necesitas primero importar el archivo";
        }

         foreach ($_DATOS_EXCEL as $filas => $registro) {

         	if ($registro["fecha_origen"]<>"" || $registro["fecha_origen"]<>null) {
         		echo $registro["cliente"];



         	//CLIENTE
		    	$nombre=$registro["cliente"];
	            $telefono=$registro["telefono"];
	            $profesion=$registro["profesion"];

	            $SQL="SELECT * FROM profesiones WHERE profesion ='".$profesion."'";
	            $resp_query=mysqli_query($con, $SQL);
	            $res_prof = mysqli_fetch_array($resp_query);
	            $id_profesion=$res_prof["id_profesion"];


	            $SQL="INSERT INTO cuestionarios_clientes(nombre,id_profesion,telefono) VALUES('$nombre','$id_profesion','$telefono')";
	            mysqli_query($con, $SQL);

	            $rs = mysql_query("SELECT MAX(id_cliente_cuestionario) AS id FROM cuestionarios_clientes LIMIT 1");
	            if ($row = mysql_fetch_row($rs)) {
	                $id_cliente= trim($row[0]);
	            }

				$usuario_siac= $registro["asesor"];

			//FECHA
				// Las fechas importadas de EXCEL estan en formato numero, lo cual busca en la tabla comparacion FECHA IMPORTACION y Obtengo la fecha correcta.

	            $SQL="SELECT fecha FROM fechas_importacion WHERE nro='".$registro["fecha_origen"]."'";
	            $res=mysqli_query($con, $SQL);
	            $cant=mysql_num_rows($res);
	            if ($cant==0) { $fecha_origen=null;}else{$fecha_reg=mysqli_fetch_array($res);
	            $fecha_origen=$fecha_reg["fecha"];

	            list($dia, $mes, $ano) = split('[/.-]', $fecha_origen);
	            $fecha_origen= strtotime($dia."-".$mes."-".$ano);
				$fecha_origen = date('Y-m-d', $fecha_origen);
	            }

	            $SQL="SELECT fecha FROM fechas_importacion WHERE nro='".$registro["fecha_encuesta"]."'";
	            $res=mysqli_query($con, $SQL);
	            $cant=mysql_num_rows($res);
	            if ($cant==0) {$fecha_encuesta=null;}else{$fecha_reg=mysqli_fetch_array($res);
	            $fecha_encuesta=$fecha_reg["fecha"];

	            list($dia, $mes, $ano) = split('[/.-]', $fecha_encuesta);
	            $fecha_encuesta= strtotime($dia."-".$mes."-".$ano);
	            $fecha_encuesta = date('Y-m-d', $fecha_encuesta);
	            }

	        //USUARIO


	            $SQL="SELECT * FROM usuarios WHERE usuario_siac ='".$registro["asesor"]."'";
	            $res_e=mysqli_query($con, $SQL);
	            if (empty($res_e)) {$id_usuario=22;}else{$usu=mysqli_fetch_array($res_e); $id_usuario=$usu["id_usuario"];}


	        //CONDICION DE TERMINADO

	            $SQL="SELECT * FROM cuestionarios_no_hechos WHERE motivo ='".$registro["condicion"]."'";
	            $res_cond=mysqli_query($con, $SQL);
	            if (empty($res_cond)) {$motivo=0;}else{$cond=mysqli_fetch_array($res_cond); $motivo= $cond["id_motivo_nohecho"];}

	            // LA VARIABLE $motivo ES LA CONDICION DE CIERRE DEL CUESTIONARIO.

	        //CUESTIONARIO

	            $modelo_version=$registro["modelo_version"]." ";


	            $SQL="INSERT INTO cuestionarios (";
	            $SQL .=" id_encuesta, ";
	            $SQL .=" fecha_muestra_origen,";
	            $SQL .=" fecha_cuestionario,";
	            $SQL .=" id_cliente_cuestionario, ";
	            $SQL .=" id_usuario, ";
	            $SQL .=" modelo_version,";
 	            $SQL .=" id_estado_cuestionario,";
	            $SQL .=" motivo,";
	            $SQL .=" activo";
	            $SQL .=" ) VALUES (";
	            $SQL .=" '2','$fecha_origen','$fecha_encuesta','$id_cliente','$id_usuario','$modelo_version','3','$motivo',1)";
	            mysqli_query($con, $SQL);

	            // consulto a la tabla CUESTIONARIO cual es el ultimo insertado
	            $SQL="SELECT MAX(id_cuestionario) AS id FROM cuestionarios LIMIT 1";
	           	$rs = mysqli_query($con, $SQL);
	            if ($row = mysql_fetch_row($rs)) {
	                $id_cuestionario= trim($row[0]);
	            }

	            $SQL="SELECT * FROM encuestas_preguntas WHERE id_encuesta = 2 AND baja = 0 ORDER BY nro_pregunta";
	    		$res=mysqli_query($con, $SQL);

				$contador_preguntas = 0;

	            while ($preguntas=mysqli_fetch_array($res)) {

	            	$contador_preguntas = $contador_preguntas + 1;

	            	if ($contador_preguntas==17) {
						$SQL="INSERT INTO cuestionarios_respuestas";
						$SQL .="(nro_pregunta ,";
						$SQL .="id_formato_respuesta ,";
						$SQL .="id_encuesta ,";
						$SQL .="id_cuestionario ,";
						$SQL .="si_respuesta ,";
						$SQL .="proxima_pregunta ,";
						$SQL .="observacion ,";
						$SQL .="id_pregunta) VALUES ";
						$SQL .="(".$preguntas["nro_pregunta"].",";
						$SQL .="".$preguntas["id_formato_respuesta"].",";
						$SQL .=" '2' ,";
						$SQL .="".$id_cuestionario.",";
						$SQL .="".$preguntas["si_respuesta"].", ";
						$SQL .="".$preguntas["proxima_pregunta"].", ";
						$SQL .=" '".$registro["preg_17"]."', ";
						$SQL .="".$preguntas["id_pregunta"].")";
						mysqli_query($con, $SQL);
	            	}
	            	if ($contador_preguntas==18) {
						$SQL="INSERT INTO cuestionarios_respuestas";
						$SQL .="(nro_pregunta ,";
						$SQL .="id_formato_respuesta ,";
						$SQL .="id_encuesta ,";
						$SQL .="id_cuestionario ,";
						$SQL .="si_respuesta ,";
						$SQL .="proxima_pregunta ,";
						$SQL .="observacion ,";
						$SQL .="id_pregunta) VALUES ";
						$SQL .="(".$preguntas["nro_pregunta"].",";
						$SQL .="".$preguntas["id_formato_respuesta"].",";
						$SQL .=" '2' ,";
						$SQL .="".$id_cuestionario.",";
						$SQL .="".$preguntas["si_respuesta"].", ";
						$SQL .="".$preguntas["proxima_pregunta"].", ";
						$SQL .=" '".$registro["preg_18"]."', ";
						$SQL .="".$preguntas["id_pregunta"].")";
						mysqli_query($con, $SQL);
	            	}


	            	if ($contador_preguntas<17) {
						$SQL="INSERT INTO cuestionarios_respuestas";
						$SQL .="(nro_pregunta ,";
						$SQL .="id_formato_respuesta ,";
						$SQL .="id_encuesta ,";
						$SQL .="id_cuestionario ,";
						$SQL .="si_respuesta ,";
						$SQL .="proxima_pregunta ,";
						$SQL .="id_pregunta) VALUES ";
						$SQL .="(".$preguntas["nro_pregunta"].",";
						$SQL .="".$preguntas["id_formato_respuesta"].",";
						$SQL .=" '2' ,";
						$SQL .="".$id_cuestionario.",";
						$SQL .="".$preguntas["si_respuesta"].", ";
						$SQL .="".$preguntas["proxima_pregunta"].", ";
						$SQL .="".$preguntas["id_pregunta"].")";
						mysqli_query($con, $SQL);
	            	}

					$rs = mysql_query("SELECT MAX(id_respuesta_cuestionario) AS id FROM cuestionarios_respuestas");
					if ($row = mysql_fetch_row($rs)) {$id_ultimo=trim($row[0]);}

					$id_tipo_respuesta = $preguntas["id_tipo_respuesta"];

					$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE id_tipo_respuesta =".$id_tipo_respuesta;
					$res_linea=mysqli_query($con, $SQL);

					if ($contador_preguntas<17) {

							$si_respuesta = 0;
							while ($lineas=mysqli_fetch_array($res_linea)) {

								 $si_respuesta = $si_respuesta + 1;

								 $valor_respuesta = 0;

								if ($registro["preg_".$preguntas["nro_pregunta"]]==$si_respuesta) {
									$valor_respuesta = 1;
									//echo $contador_preguntas . "  ".$valor_respuesta . " <br> ";
								}

								if ($registro["preg_".$preguntas["nro_pregunta"]]=="SI" AND $si_respuesta==1) {
									$valor_respuesta = 1;
								}

								if ($registro["preg_".$preguntas["nro_pregunta"]]=="NO" AND $si_respuesta==2) {
									$valor_respuesta = 1;
								}

								$SQL="INSERT INTO cuestionarios_respuestas_lineas (";
								$SQL .=" id_cuestionario, id_respuesta_cuestionario,id_linea_tipo_respuesta, linea_tipo_respuesta, respuesta) VALUES(".$id_cuestionario.",".$id_ultimo.",".$lineas["id_linea_tipo_respuesta"].",'".$lineas["linea_tipo_respuesta"]."',".$valor_respuesta.")";
								mysqli_query($con, $SQL);
							}
						}

				}



	} // fin del if que condiciona que traiga un dato la fecha de origen

}

//

       // unlink($destino);


       // header("Location: cuestionarios_pendientes.php");

 ?>