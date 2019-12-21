<?php
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="SELECT
reservas.idreserva AS idreserva,
reservas.idmodelo AS idmodelo
FROM
reservas
WHERE
reservas.tipoprecio =  'abierto' AND
reservas.cancelada =  '0' AND
reservas.anulada =  '0' AND
reservas.entregada =  '0' AND
reservas.compra =  'Nuevo'";

$res=mysqli_query($con, $SQL);
$cont=0;

while ($reserva=mysqli_fetch_array($res)) {
	$SQL="SELECT * FROM listaprecio WHERE idmodelo='".$reserva['idmodelo']."'";
	$precios=mysqli_query($con, $SQL);
	$precio=mysqli_fetch_array($precios);

	$SQL="UPDATE lineas_detalle SET monto='".$precio['pl']."'WHERE idreserva='".$reserva['idreserva']."' and idcodigo = 1";
	mysqli_query($con, $SQL);
	$SQL="UPDATE lineas_detalle SET monto='".$precio['flete']."'WHERE idreserva='".$reserva['idreserva']."' and idcodigo = 2";
	mysqli_query($con, $SQL);
	$SQL="UPDATE lineas_detalle SET monto='".$precio['trans']."'WHERE idreserva='".$reserva['idreserva']."' and idcodigo = 3";
	mysqli_query($con, $SQL);

	$cont=$cont+1;

};

echo "<script type='text/javascript'>
       alert('Los Precios se actualizaron correctamente... La cantidad de Reservas afectadas: ".$cont."');
       document.location.href = '../_admin/precios_admin.php';
       </script>";
?>