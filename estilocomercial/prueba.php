<?php
include("funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE cuestionarios SET ";
        $SQL .=" fecha_cuestionario = '".date("Y-m-d")."',";
		$SQL .=" id_estado_cuestionario = 3,"; //terminado
		$SQL .=" motivo = 11,";
		$SQL .=" comentario= CONCAT(comentario,' - Encuesta TASA')";
  		$SQL .=" WHERE id_encuesta = 1 AND id_estado_cuestionario < 3 AND año_unidad = 1000";
		mysqli_query($con, $SQL);
        ?>