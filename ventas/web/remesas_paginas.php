<?php

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	//$SQL="SELECT * FROM reservas WHERE idusuario =".$_POST['idusu']." AND anulada <> 1 AND entregada < 3 AND ORDER BY idreserva DESC";
	//$SQL = "SELECT reservas.*, clientes.nombre FROM reservas, clientes WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre <> '' AND clientes.nombre LIKE '%".$_POST["buscar"]."%'";
	$SQL="SELECT
	reservas.idreserva AS idreserva,
	reservas.fecres AS fecres,
	reservas.compra AS compra,
	clientes.nombre AS cliente,
	usuarios.nombre AS asesor,
	reservas.detalleu AS detalleu,
	reservas.idgrupo,
	reservas.idmodelo,
	reservas.idfactura AS idfactura,
	reservas.enviada as enviada,
	facturas.fecped AS fecped,
	facturas.estado AS estado
FROM
reservas
Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
Inner Join clientes ON reservas.idcliente = clientes.idcliente
Inner Join facturas ON reservas.idfactura = facturas.idfactura
WHERE
reservas.anulada =  '0' AND facturas.estado > 0
ORDER BY facturas.fecped DESC
LIMIT ".$_POST["inicio"]." , 20";

$res=mysqli_query($con, $SQL);

include("remesa_cuerpo.php");
 mysqli_close($con);

?>