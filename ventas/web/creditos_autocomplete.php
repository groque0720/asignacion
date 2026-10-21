<?php
include("../includes/security.php");   // exige login + arranca sesion

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();

//recibo el dato a buscar, ESCAPADO (corta la SQLi en el LIKE).
//Sin utf8_decode: la conexion es utf8 y con utf8_decode las busquedas con tilde/ñ dan error de collation.
$datoBuscar = mysqli_real_escape_string($con, $_GET["term"] ?? "");

//busco un valor aproximado al dato escrito
$ssql="SELECT
	nombre
	FROM
	clientes
	WHERE
	nombre LIKE '%" . $datoBuscar . "%'
	LIMIT 10";
$rs = mysqli_query($con, $ssql);   // fix: era mysql_query (removida en PHP7)

//creo el array de los elementos sugeridos
$arrayElementos = array();

//bucle para meter todas las sugerencias de autocompletar en el array
while ($fila = mysqli_fetch_array($rs)){
    array_push($arrayElementos, array("id"=>$fila['nombre'],"label"=>$fila['nombre'],"value"=>$fila['nombre'])) ;
}
 mysqli_close($con);
echo json_encode($arrayElementos);
?>
