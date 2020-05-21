<?php

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	//$SQL="SELECT * FROM reservas WHERE idusuario =".$_POST['idusu']." AND anulada <> 1 AND entregada < 3 AND ORDER BY idreserva DESC";
	//$SQL = "SELECT reservas.*, clientes.nombre FROM reservas, clientes WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre <> '' AND clientes.nombre LIKE '%".$_POST["buscar"]."%'";
	$SQL="SELECT
reservas.idreserva,
clientes.nombre AS cliente,
reservas.idtipo,
reservas.idgrupo,
reservas.idmodelo,
reservas.compra AS compra,
usuarios.nombre AS asesor,
reservas.detalleu AS detalleu,
reservas.fecres,
reservas.enviada AS enviada,
reservas.estadopago AS estadopago,
reservas.idcliente as idcliente,
reservas.anulada,
reservas.idfactura
FROM
reservas
Inner Join clientes ON clientes.idcliente = reservas.idcliente
Inner Join usuarios ON usuarios.idusuario = reservas.idusuario
WHERE
reservas.enviada >=  '1'
ORDER BY
enviada ASC, reservas.fecres DESC LIMIT ".$_POST["inicio"]." , 20";

$res=mysqli_query($con, $SQL);
$usuario_id = $_POST["usuario_id"];

include("control_reservas_cuerpo.php");
 mysqli_close($con);

?>