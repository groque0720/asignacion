<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

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

            for ($i = 1; $i <= 100; $i++) {
            	if ($i>1) {
	                $_DATOS_EXCEL[$i]['id_encuesta'] = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['fecha_origen'] = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['cliente'] = $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['telefono'] = $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['email'] = $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['localidad'] = $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['modelo_version'] = $objPHPExcel->getActiveSheet()->getCell('G' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['año'] = $objPHPExcel->getActiveSheet()->getCell('H' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['dominio'] = $objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['concesionario_vendedor'] = $objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['asesor'] = $objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['descripcion_servicio'] = $objPHPExcel->getActiveSheet()->getCell('L' . $i)->getCalculatedValue();
	                $_DATOS_EXCEL[$i]['sucursal_dyv'] = $objPHPExcel->getActiveSheet()->getCell('M' . $i)->getCalculatedValue();
            	}

            }
	}else {
            echo "Necesitas primero importar el archivo";
        }

         foreach ($_DATOS_EXCEL as $filas => $registro) {



        	if ($registro["id_encuesta"]<>"") {
		    $nombre=$registro["cliente"];
	            $email=$registro["email"];
	            $localidad=$registro["localidad"];
	            $telefono=$registro["telefono"];

	            $usuario_siac= $registro["asesor"];

	            $SQL="INSERT INTO cuestionarios_clientes(nombre,email,localidad,telefono) VALUES('$nombre','$email','$localidad','$telefono')";
	            mysqli_query($con, $SQL);

	            $rs = mysql_query("SELECT MAX(id_cliente_cuestionario) AS id FROM cuestionarios_clientes LIMIT 1");
	            if ($row = mysql_fetch_row($rs)) {
	                $id_cliente= trim($row[0]);
	            }

	            $SQL="SELECT fecha FROM fechas_importacion WHERE nro='".$registro["fecha_origen"]."'";
	            $res=mysqli_query($con, $SQL);
	            if (empty($res)) { $fecha_reg["fecha"]="";}else{$fecha_reg=mysqli_fetch_array($res); }
	            $fecha=$fecha_reg["fecha"];


	            list($dia, $mes, $año) = split('[/.-]', $fecha);
	            $fecha= strtotime($dia."-".$mes."-".$año);
	            $fecha = date('Y-m-d', $fecha);
	            // $id_usuario=null;

	             $id_usuario=22;


	            $SQL="SELECT * FROM usuarios WHERE usuario_siac ='".$registro["asesor"]."'";
	            $res_e=mysqli_query($con, $SQL);

	            if (empty($res_e)) {$usu["id_usuario"]='22';}else{$usu=mysqli_fetch_array($res_e); $id_usuario=$usu["id_usuario"];}

	            $modelo_version=$registro["modelo_version"]." ";
	            $año= $registro["año"];
	            $dominio = $registro["dominio"];
	            $concesionario_vendedor=$registro["concesionario_vendedor"];
	            $id_encuesta=(int)$registro["id_encuesta"];
	            $asesor_siac = $registro["asesor"];
	            $descripcion_servicio = $registro['descripcion_servicio'];

	           if ($registro['sucursal_dyv']!='') {
	            	$sucursal_dyv = $registro['sucursal_dyv'];
	            }else{
	            	$sucursal_dyv = 0;
	            }

	            $SQL="INSERT INTO cuestionarios (";
	            $SQL .=" id_encuesta, ";
	            $SQL .=" fecha_muestra_origen,";
	            $SQL .=" id_cliente_cuestionario, ";
	            $SQL .=" id_usuario, ";
	            $SQL .=" asesor_siac,";
	            $SQL .=" modelo_version,";
	            $SQL .=" año_unidad,";
	            $SQL .=" dominio,";
	            $SQL .=" concesionario_vendedor,";
	            $SQL .=" id_estado_cuestionario,";
	            $SQL .=" descripcion_servicio,";
	            $SQL .=" sucursal_dyv,";
	            $SQL .=" activo";
	            $SQL .=" ) VALUES (";
	            $SQL .=" '$id_encuesta','$fecha','$id_cliente','$id_usuario','$asesor_siac','$modelo_version','$año','$dominio','$concesionario_vendedor',1,'$descripcion_servicio', $sucursal_dyv,1)";

			mysqli_query($con, $SQL);

		}
	}

        unlink($destino);
        header("Location: cuestionarios_pendientes.php");

 ?>