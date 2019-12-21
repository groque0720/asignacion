<?php
//recibo el dato que deseo buscar sugerencias
$datoBuscar = utf8_decode($_GET["term"]);

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

//busco un valor aproximado al dato escrito
// $ssql = "SELECT * FROM codigos WHERE idcodigo <> 1 AND idcodigo <> 2 AND  idcodigo <> 3 AND detalle LIKE '%" . $datoBuscar . "%'";
$ssql="SELECT
	nombre
	FROM
	clientes
	WHERE
	nombre LIKE '%" . $datoBuscar . "%'
	LIMIT 10";
//$ssql = "SELECT * FROM codigos WHERE  detalle LIKE '%" . $datoBuscar . "%'";
//echo $ssql;
$rs = mysql_query($ssql);
 mysqli_close($con);

//creo el array de los elementos sugeridos
$arrayElementos = array();

//bucle para meter todas las sugerencias de autocompletar en el array
while ($fila = mysqli_fetch_array($rs)){
    array_push($arrayElementos, array("id"=>$fila['nombre'],"label"=>$fila['nombre'],"value"=>$fila['nombre'])) ;
}
echo json_encode($arrayElementos);
?>