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
	reservas.idreserva AS idreserva,
	reservas.fecres AS fecres,
	reservas.compra AS compra,
	clientes.nombre AS cliente,
	usuarios.nombre AS asesor,
	tipos_creditos.tipocredito AS credito,
	financieras.financiera AS financiera,
	lineas_detalle.monto AS monto,
	reservas.detalleu AS detalleu,
	reservas.idgrupo,
	reservas.idmodelo,
	reservas.idcredito  AS idcredito,
	reservas.idfactura AS idfactura,
	creditos.estado as estado
	FROM
	reservas
	Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
	Inner Join codigos ON lineas_detalle.idcodigo = codigos.idcodigo
	Inner Join tipos_creditos ON codigos.tipocredito = tipos_creditos.idtipocredito
	Inner Join financieras ON codigos.financiera = financieras.idfinanciera
	Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
	Inner Join clientes ON reservas.idcliente = clientes.idcliente
	Inner Join creditos ON reservas.idcredito = creditos.idcredito
	WHERE
	codigos.credito =  '1' AND
	reservas.anulada =  '0' AND clientes.nombre LIKE '%" . $datoBuscar . "%'
	ORDER BY estado ASC,
	idreserva DESC LIMIT 15";
//$ssql = "SELECT * FROM codigos WHERE  detalle LIKE '%" . $datoBuscar . "%'";
//echo $ssql;
$rs = mysql_query($ssql);
 mysqli_close($con);

//creo el array de los elementos sugeridos
$arrayElementos = array();

//bucle para meter todas las sugerencias de autocompletar en el array
while ($fila = mysqli_fetch_array($rs)){
    array_push($arrayElementos, array("id"=>$fila['idreserva'],"label"=>$fila['cliente'],"value"=>$fila['cliente'])) ;
}
echo json_encode($arrayElementos);
?>