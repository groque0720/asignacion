<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	//$SQL="SELECT * FROM reservas WHERE idusuario =".$_POST['idusu']." AND anulada <> 1 AND entregada < 3 AND ORDER BY idreserva DESC";
	//$SQL = "SELECT reservas.*, clientes.nombre FROM reservas, clientes WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre <> '' AND clientes.nombre LIKE '%".$_POST["buscar"]."%'";
	$SQL="SELECT
reservas.idreserva,
clientes.nombre AS cliente,
reservas.idtipo,
reservas.idgrupo,
reservas.idmodelo,
reservas.compra AS compra,
reservas.detalleu AS detalleu,
usuarios.nombre AS asesor,
reservas.fecres,
reservas.enviada AS enviada
FROM
reservas
Inner Join clientes ON clientes.idcliente = reservas.idcliente
Inner Join usuarios ON usuarios.idusuario = reservas.idusuario
WHERE
(reservas.idreserva LIKE '%".$_POST["buscar"]."%' OR clientes.nombre  LIKE '%".$_POST["buscar"]."%') AND
reservas.enviada >=  '1'
ORDER BY
enviada ASC, reservas.fecres DESC
LIMIT 20";

$res=mysqli_query($con, $SQL);
include("control_reservas_cuerpo.php");
 mysqli_close($con);
?>