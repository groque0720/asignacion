<?php

include("../_seguridad/_seguridad.php");
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$datoBuscar = utf8_decode($_GET["term"]);

$SQL="SELECT DISTINCT cuestionarios_clientes.nombre AS cliente,
cuestionarios.id_encuesta AS id_encuesta FROM cuestionarios
INNER JOIN cuestionarios_clientes ON cuestionarios.id_cliente_cuestionario = cuestionarios_clientes.id_cliente_cuestionario WHERE
cuestionarios_clientes.nombre LIKE '%".$datoBuscar."%' AND
cuestionarios.activo = 1 AND cuestionarios.id_estado_cuestionario = 3  ORDER BY cuestionarios.fecha_muestra_origen ASC LIMIT 200";
$res=mysqli_query($con, $SQL);


//creo el array de los elementos sugeridos
$arrayElementos = array();

//bucle para meter todas las sugerencias de autocompletar en el array
while ($fila = mysqli_fetch_array($res)){
    array_push($arrayElementos, array("id"=>$fila['id_encuesta'],"label"=>$fila['cliente'],"value"=>$fila['cliente'])) ;
}
echo json_encode($arrayElementos);

mysqli_close($con);	 ?>