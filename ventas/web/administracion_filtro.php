<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	//$SQL="SELECT * FROM reservas WHERE idusuario =".$_POST['idusu']." AND anulada <> 1 AND entregada < 3 AND ORDER BY idreserva DESC";
	//$SQL = "SELECT reservas.*, clientes.nombre FROM reservas, clientes WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre <> '' AND clientes.nombre LIKE '%".$_POST["buscar"]."%'";
	$SQL="SELECT reservas.*, clientes.nombre FROM clientes INNER JOIN reservas ON clientes.idcliente = reservas.idcliente WHERE reservas.anulada = 0 AND reservas.enviada >= 1 AND clientes.nombre  LIKE '%".$_POST["buscar"]."%' ORDER BY clientes.nombre LIMIT 10";
	$res=mysqli_query($con, $SQL);
?>

<?php include("administracion_cuerpo.php") ?>