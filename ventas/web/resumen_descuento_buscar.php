<?php
//recibo el dato que deseo buscar sugerencias
$datoBuscar = $_POST["abuscar"];

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="SELECT
	lineas_detalle.monto as monto,
	reservas.idreserva,
	clientes.nombre AS cliente,
	reservas.idtipo,
	reservas.idgrupo,
	reservas.idmodelo,
	reservas.compra AS compra,
	reservas.detalleu AS detalleu,
	usuarios.nombre AS asesor,
	reservas.fecres,
	reservas.enviada AS enviada,
	reservas.idcliente as idcliente
	FROM
	reservas
	Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
	Inner Join clientes ON clientes.idcliente = reservas.idcliente
	Inner Join usuarios ON usuarios.idusuario = reservas.idusuario
	WHERE
	lineas_detalle.monto <  '0' AND fecres >= '".$datoBuscar."' AND not isnull(compra) AND anulada <> 1 AND entregada < 3  ORDER BY idreserva";
$res = mysqli_query($con, $SQL);

include("control_reservas_cuerpo.php");
 mysqli_close($con);
?>
