<?php
//recibo el dato que deseo buscar sugerencias
$datoBuscar = utf8_decode($_GET["term"]);

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

//busco un valor aproximado al dato escrito
// $ssql = "SELECT * FROM codigos WHERE idcodigo <> 1 AND idcodigo <> 2 AND  idcodigo <> 3 AND detalle LIKE '%" . $datoBuscar . "%'";

$ssql = "SELECT * FROM notificaciones WHERE  cliente LIKE '%" . $datoBuscar . "%' ORDER BY cliente ASC	LIMIT 15";
//echo $ssql;
$rs = mysql_query($ssql);


//creo el array de los elementos sugeridos
$arrayElementos = array();

//bucle para meter todas las sugerencias de autocompletar en el array
while ($fila = mysqli_fetch_array($rs)){
    array_push($arrayElementos, array("id"=>$fila['idnotificaciones'],"label"=>$fila['cliente'],"value"=>$fila['cliente'])) ;
}
 mysqli_close($con);
echo json_encode($arrayElementos);
?>