<?php

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");


$SQL="SELECT reservas.idcredito
FROM lineas_detalle
Inner Join codigos ON codigos.idcodigo = lineas_detalle.idcodigo
Inner Join reservas ON reservas.idreserva = lineas_detalle.idreserva
WHERE lineas_detalle.idlinea = ".$_POST['idfila'];

$cred=mysqli_query($con, $SQL);

if (!empty($cred)) {
$credito=mysqli_fetch_array($cred);
$SQL="UPDATE creditos SET";
$SQL .=" estado = 0 ";
$SQL .=" WHERE idcredito =".$credito['idcredito'];
mysqli_query($con, $SQL);
}


$SQL="DELETE  FROM lineas_detalle WHERE idlinea =".$_POST['idfila'];
mysqli_query($con, $SQL); ?>

<?php include("reserva_altamodbaja_sol.php");

 mysqli_close($con); ?>

