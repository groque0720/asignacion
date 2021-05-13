<?php
//recibo el dato que deseo buscar sugerencias
$datoBuscar = $_POST["abuscar"];

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
////mysql_query("SET NAMES 'utf8'");

//busco un valor aproximado al dato escrito
// $ssql = "SELECT * FROM codigos WHERE idcodigo <> 1 AND idcodigo <> 2 AND  idcodigo <> 3 AND detalle LIKE '%" . $datoBuscar . "%'";

$FIL="";

	// if ($_POST["est"]==1) {
	// 	$FIL =" AND not isnull(reservas.llego) AND reservas.llego <> 0" ; // llegadas todas
	// }

	// if ($_POST["est"]==11) {
	// 	$FIL =" AND reservas.cancelada = 0 AND not isnull(reservas.llego) AND reservas.llego <> 0" ; // llegadas no canceladas
	// }

	// if ($_POST["est"]==12) {
	// 	$FIL =" AND reservas.cancelada = 1 AND not isnull(reservas.llego) AND reservas.llego <> 0" ; // llegadas Canceladas
	// }

	// if ($_POST["est"]==2) {
	// 	$FIL =" AND (isnull(reservas.llego) OR reservas.llego = '')" ; // no llegadas todas
	// }

	// if ($_POST["est"]==21) {
	// 	$FIL =" AND reservas.cancelada = 1 AND (isnull(reservas.llego) OR reservas.llego = '')" ; // no llegadas Canceladas
	// }

	// if ($_POST["est"]==3) {
	// 	$FIL ="  AND reservas.cancelada = 0 AND ((not isnull(reservas.llego) OR reservas.llego <> 0) AND datediff (curdate(), reservas.llego ) > 10  )" ; //llegadas mas de 10 dias
	// }
	// if ($_POST["est"]==4) {
	// 	$FIL =" AND reservas.cancelada = 0 AND not isnull(reservas.llego) AND reservas.llego <> 0 AND (datediff (curdate(), reservas.fechacanc ) > 0 OR reservas.fechacanc = 0 or reservas.fechacanc ='' )" ; // cancelaciones vencidas
	// }
	//
	//

$datoBuscar = explode(" - ", $datoBuscar);
$datoBuscar = $datoBuscar[0];

$SQL="SELECT
reservas.idreserva AS idreserva,
reservas.nrounidad AS nrounidad,
reservas.interno AS interno,
clientes.nombre AS cliente,
usuarios.nombre AS asesor,
reservas.idgrupo AS idgrupo,
reservas.fecres AS fecres,
reservas.idmodelo AS idmodelo,
reservas.nroorden AS nroorden,
reservas.llego AS llego,
reservas.compra AS compra,
reservas.detalleu AS detalleu,
reservas.obscanc as obs,
reservas.fechacanc as fechacanc,
reservas.enviada as enviada,
reservas.idfactura as idfactura,
reservas.idcredito as idcredito,
reservas.estadopago as estadopago,
reservas.cancelada as cancelada,
reservas.idcliente as idcliente
FROM
clientes
Inner Join reservas ON clientes.idcliente = reservas.idcliente
Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
Inner Join facturas ON reservas.idfactura = facturas.idfactura
WHERE reservas.entregada < 3
AND reservas.enviada >= '1' AND
( clientes.nombre LIKE '%" . $datoBuscar . "%' OR
clientes.nrodoc LIKE '%" . $datoBuscar . "%' OR
clientes.tfijo LIKE '%" . $datoBuscar . "%' OR
clientes.tcelu LIKE '%" . $datoBuscar . "%' OR
 clientes.nombre LIKE '%" . $datoBuscar . "%' OR
 reservas.idreserva LIKE '%" . $datoBuscar . "%' OR
 reservas.nroorden LIKE '%" . $datoBuscar . "%' OR
 reservas.nrounidad LIKE '%" . $datoBuscar . "%' OR
 reservas.interno LIKE '%" . $datoBuscar . "%' OR
facturas.nombre LIKE '%" . $datoBuscar . "%' )";
$SQL .= $FIL." ORDER BY usuarios.nombre, clientes.nombre";

$res = mysqli_query($con, $SQL);
include('control_pagos_cliente_cuerpo.php');
 mysqli_close($con);
?>
