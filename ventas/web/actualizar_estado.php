<?php
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="SELECT * FROM datosasignacion";
$res=mysqli_query($con, $SQL);

while ($reg=mysqli_fetch_array($res)) {

	$SQL="UPDATE reservas SET";
	$SQL .="  nrounidad = ".$reg["nrounidad"];
	$SQL .=" WHERE nroorden = ".$reg["nroorden"];
	mysqli_query($con, $SQL);

}

mysqli_close($con);

echo "Se actualizo correctamente";
  ?>