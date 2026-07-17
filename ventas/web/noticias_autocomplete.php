<?php
include("../includes/security.php");   // exige login + arranca sesion

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();

//recibo el dato a buscar, ESCAPADO (corta la SQLi en el LIKE)
$datoBuscar = mysqli_real_escape_string($con, utf8_decode($_GET["term"] ?? ""));

//busco un valor aproximado al dato escrito
$ssql = "SELECT * FROM notificaciones WHERE cliente LIKE '%" . $datoBuscar . "%' ORDER BY cliente ASC LIMIT 15";
$rs = mysqli_query($con, $ssql);   // fix: era mysql_query (removida en PHP7)

//creo el array de los elementos sugeridos
$arrayElementos = array();

//bucle para meter todas las sugerencias de autocompletar en el array
while ($fila = mysqli_fetch_array($rs)){
    array_push($arrayElementos, array("id"=>$fila['idnotificaciones'],"label"=>$fila['cliente'],"value"=>$fila['cliente'])) ;
}
 mysqli_close($con);
echo json_encode($arrayElementos);
?>
