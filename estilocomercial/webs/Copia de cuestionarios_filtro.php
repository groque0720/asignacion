<?php

include("../_seguridad/_seguridad.php");
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

if ($_POST["cad"]<>0) {
$cad= "AND id_encuesta = '".$_POST["cad"]."'";
}else{
	$cad="";
}

$SQL="SELECT * FROM cuestionarios
INNER JOIN cuestionarios_clientes ON cuestionarios.id_cliente_cuestionario = cuestionarios_clientes.id_cliente_cuestionario WHERE
cuestionarios_clientes.nombre LIKE '%".$_POST["buscar"]."%' AND
cuestionarios.activo = 1 AND cuestionarios.id_estado_cuestionario <> 3 ".$cad." ORDER BY cuestionarios.fecha_muestra_origen ASC LIMIT 300";
$res=mysqli_query($con, $SQL);

$cant=mysql_num_rows($res);

include("cuestionario_lista_cuerpo.php");
if ($cant==0) {
	echo "No se encontró al cliente buscado o no tiene cuestionario en la encuesta seleccionada";
}

mysqli_close($con);	 ?>