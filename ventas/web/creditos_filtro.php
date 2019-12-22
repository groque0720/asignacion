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
				WHERE clientes.nombre  LIKE '%".$_POST["buscar"]."%' AND
				codigos.credito =  '1' AND
				reservas.anulada =  '0'
				ORDER BY estado ASC,
				idreserva DESC LIMIT 15";

$res=mysqli_query($con, $SQL);
include("credito_cuerpo.php");
 mysqli_close($con);
?>