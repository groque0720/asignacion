<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");


$SQL="SELECT * FROM listaprecio WHERE idmodelo='".$_POST['elegido']."'";
$lis=mysqli_query($con, $SQL);
$lista=mysqli_fetch_array($lis);


$SQL="SELECT * FROM lineas_detalle WHERE idreserva='".$_POST['nrores']."'";
$result=mysqli_query($con, $SQL);
if (empty($result)){$cantidad=0;}else{$cantidad=mysql_num_rows($result);} ;


if ($cantidad > 1) {

	$SQL="UPDATE lineas_detalle SET monto='".$lista['pl']."', moneda = '".$lista['moneda']."' WHERE idcodigo =1 AND idreserva='".$_POST['nrores']."'";
mysqli_query($con, $SQL);

$SQL="UPDATE lineas_detalle SET monto='".$lista['flete']."' WHERE idcodigo =2 AND idreserva='".$_POST['nrores']."'";
mysqli_query($con, $SQL);

$SQL="UPDATE lineas_detalle SET monto='".$lista['trans']."' WHERE idcodigo =3 AND idreserva='".$_POST['nrores']."'";
mysqli_query($con, $SQL);
} else {

		$SQL="INSERT INTO lineas_detalle (idcodigo, idreserva, detalle, movimiento, moneda, monto) VALUES ('1', '".$_POST['nrores']."', 'Precio de Lista Unidad 0km','1','".$lista['moneda']."', ".$lista['pl'].")";
		mysqli_query($con, $SQL);
		$SQL="INSERT INTO lineas_detalle (idcodigo, idreserva, detalle, movimiento, monto) VALUES ('2', '".$_POST['nrores']."', 'Flete y 01','1',".$lista['flete'].")";
		mysqli_query($con, $SQL);
		$SQL="INSERT INTO lineas_detalle (idcodigo, idreserva, detalle, movimiento, monto) VALUES ('3', '".$_POST['nrores']."', 'Transferencia e Inscripcion','1',".$lista['trans'].")";
		mysqli_query($con, $SQL);

}

	include("reserva_altamodbaja_sol.php");
	 mysqli_close($con);
	?>