<?php

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	$operacion=$_POST["operacion"];



	if ($operacion=="terminado_masivo") {
		$id_encuesta = $_POST["id_encuesta"];

		$SQL="UPDATE cuestionarios SET ";
		$SQL .=" fecha_cuestionario ='".date('Y-m-d')."', ";
		$SQL .=" id_estado_cuestionario = 3,";
		$SQL .=" motivo = 7,";
		$SQL .=" comentario= CONCAT(comentario,' - Terminado Masivo')";
		$SQL .=" WHERE id_encuesta = ".$_POST["id_encuesta"]." AND id_estado_cuestionario < 3";
		mysqli_query($con, $SQL);
		echo " <div class='ed-container total'>
					<div class='ed-item centrar-texto'>
						Se pasaron a Terminados Masivos a todos los cuestionarios pendientes de la encuesta seleccionada
					</div>
				</div>";
	}

	if ($operacion=="nuevo_custionario") {

		$SQL="INSERT INTO cuestionarios_clientes(localidad) VALUES('')";
		mysqli_query($con, $SQL);

		$rs = mysql_query("SELECT MAX(id_cliente_cuestionario) AS id FROM cuestionarios_clientes LIMIT 1");
		if ($row = mysql_fetch_row($rs)) {
			$id_cliente= trim($row[0]);
		}

		$SQL="INSERT INTO cuestionarios(fecha_cuestionario, id_cliente_cuestionario) VALUES ('".date("Y-m-d")."',".$id_cliente." )";
		mysqli_query($con, $SQL);

		$rs = mysql_query("SELECT MAX(id_cuestionario) AS id FROM cuestionarios");
		if ($row = mysql_fetch_row($rs)) {



			echo trim($row[0]);
		}
	};

	if ($operacion=="editar") {
		$id_cuestionario=$_POST["id_cuestionario"];

		$SQL="UPDATE cuestionarios SET ";
		$SQL .=" fecha_cuestionario ='".$_POST["fecha_cuestionario"]."',";
		$SQL .=" fecha_muestra_origen ='".$_POST["fecha_muestra_origen"]."', ";
		$SQL .=" id_usuario =".$_POST["id_usuario"].",";
		$SQL .=" modelo_version ='".$_POST["modelo_version"]."',";
		$SQL .=" dominio ='".$_POST["dominio"]."', ";
		$SQL .=" año_unidad ='".$_POST["año_unidad"]."',";
		$SQL .=" id_estado_cuestionario =".$_POST["id_estado_cuestionario"].", ";
		$SQL .=" motivo =".$_POST["motivo"].", ";
		$SQL .=" concesionario_vendedor ='".$_POST["concesionario_vendedor"]."', ";
		$SQL .=" comentario ='".$_POST["comentario"]."', ";
		$SQL .=" caracter =".$_POST["caracter"].", ";
		$SQL .=" activo = 1 ";
		$SQL .=" WHERE id_cuestionario =".$id_cuestionario;
		mysqli_query($con, $SQL);


		$SQL="UPDATE cuestionarios_clientes SET ";
		$SQL .=" nombre ='".$_POST["cliente"]."', ";
		$SQL .=" email = '".$_POST["email"]."', ";
		$SQL .=" id_profesion = ".$_POST["profesion"].", ";
		$SQL .=" localidad = '".$_POST["localidad"]."', " ;
		$SQL .=" telefono = '".$_POST["telefono"]."' " ;
		$SQL .=" WHERE id_cliente_cuestionario = ".$_POST["id_cliente"];
		mysqli_query($con, $SQL);

		header("Location: cuestionario.php?id=".$id_cuestionario."&cue=".$_POST["id_cue"]);

		// if ($_POST["id_estado_cuestionario"]==3) {
		// 		header("Location: cuestionarios_terminados.php");
		// 	}else{
		// 		header("Location: cuestionarios_pendientes.php");
		// 	}
	}


	mysqli_close($con);

 ?>