<?php

	include("../funciones/func_mysql.php");
	conectar();
	////mysql_query("SET NAMES 'utf8'");
	//$SQL="SELECT * FROM reservas WHERE idusuario =".$_POST['idusu']." AND anulada <> 1 AND entregada < 3 AND ORDER BY idreserva DESC";
	//$SQL = "SELECT reservas.*, clientes.nombre FROM reservas, clientes WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre <> '' AND clientes.nombre LIKE '%".$_POST["buscar"]."%'";
	$SQL="SELECT
reservas.idreserva AS idreserva,
reservas.fecres AS fecres,
reservas.compra AS compra,
clientes.nombre AS cliente,
usuarios.nombre AS asesor,
reservas.detalleu AS detalleu,
reservas.idcliente AS idcliente,
reservas.idgrupo,
reservas.idmodelo,
reservas.idcredito,
reservas.estadopago as estadopago
FROM
reservas
Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
Inner Join clientes ON reservas.idcliente = clientes.idcliente
WHERE
reservas.anulada <> '1' AND
reservas.enviada >=  '1' AND usuarios.idsucursal =".$_POST["idsuc"]."
ORDER BY
cliente ASC,
asesor ASC LIMIT ".$_POST["inicio"]." , 15";

$res=mysqli_query($con, $SQL);

include("pagos_cliente_cuerpo.php");
 mysqli_close($con);

?>