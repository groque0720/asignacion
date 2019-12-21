<?php
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

echo '<script language = javascript>
	alert("Facturacion Activa");
	</script>';

$SQL="SELECT idfactura FROM reservas WHERE idreserva=".$_POST['idres'];
$id_fact=mysqli_query($con, $SQL);
$factura=mysqli_fetch_array($id_fact);

$SQL="SELECT * FROM lineas_detalle WHERE idreserva =".$_POST['idres'];
$lin_res=mysqli_query($con, $SQL);

$SQL="SELECT * FROM facturas_lineas WHERE idfactura =".$factura['idfactura'];
$result = mysqli_query($con, $SQL);
$cant = mysql_num_rows($result);

if ($cant == 0) {

	while ($lineas=mysqli_fetch_array($lin_res)) {

	$SQL="INSERT INTO facturas_lineas (idcodigo, codigo, idfactura, detalle, adjunto, movimiento , monto)";
	$SQL .=" VALUES ('".$lineas['idcodigo']."','".$lineas['codigo']."','".$factura['idfactura']."','".$lineas['detalle']."','".$lineas['adjunto']."','".$lineas['movimiento']."',".$lineas['monto'].")";
	mysqli_query($con, $SQL);
	}
}
?>