<?php

session_start();

include("../funciones/func_mysql.php");
conectar();
//mysqli_query("SET NAMES 'utf8'");

//$SQL="INSERT INTO clientes(activo) VALUES (1)";
$SQL="INSERT INTO clientes(activo) VALUES (1)";
mysqli_query($con, $SQL);

$SQL = "SELECT MAX(idcliente) AS id FROM clientes";

// $rs = mysqli_query("SELECT MAX(idcliente) AS id FROM clientes");
$rs = mysqli_query($con, $SQL);
if ($row = mysqli_fetch_row($rs)) {
$idcliente = trim($row[0]);
}


$SQL="INSERT INTO entregausado(marca) VALUES ('.')";
mysqli_query($con, $SQL);

$SQL="SELECT MAX(identregau) AS id FROM entregausado";

// $rs = mysqli_query("SELECT MAX(identregau) AS id FROM entregausado");

$rs = mysqli_query($con, $SQL);
if ($row = mysqli_fetch_row($rs)) {
$identregau = trim($row[0]);
}

$SQL="INSERT INTO facturas(anombre, estado) VALUES ('propio', 0)";
mysqli_query($con, $SQL);

$rs = mysqli_query($con,"SELECT MAX(idfactura) AS id FROM facturas");
if ($row = mysqli_fetch_row($rs)) {
$idfactura = trim($row[0]);
}

$SQL="INSERT INTO facturas(anombre, estado) VALUES ('propio', 0)";
mysqli_query($con, $SQL);

$rs = mysqli_query($con, "SELECT MAX(idfactura) AS id FROM facturas");
if ($row = mysqli_fetch_row($rs)) {
$idfactura = trim($row[0]);
}

$SQL="INSERT INTO creditos(estado, activo) VALUES (0, 1)";
mysqli_query($con, $SQL);

$rs = mysqli_query($con,"SELECT MAX(idcredito) AS id FROM creditos");
if ($row = mysqli_fetch_row($rs)) {
$idcredito = trim($row[0]);
}

$SQL="INSERT INTO reservas(idusuario, fecres, hora, idcliente, identregau, idfactura, idcredito, enviada, anulada, entregada, cancelada, entregadoc, marca)";
$SQL .=" VALUES (".$_SESSION["id"].", '".date("Y-m-d")."', '".date("H:i:s")."', ".$idcliente.",".$identregau.",".$idfactura.",".$idcredito.",0,0,0,0,0, 'Toyota')";
mysqli_query($con, $SQL);


$rs = mysqli_query($con,"SELECT MAX(idreserva) AS id FROM reservas");
if ($row = mysqli_fetch_row($rs)) {
	$idreserva = trim($row[0]);
}


$SQL="INSERT INTO pagos(idreserva,cancelado) VALUES(".$idreserva.", 0)";
mysqli_query($con, $SQL);

 mysqli_close($con);
header("Location: reserva.php?IDrecord=$idreserva");

 ?>