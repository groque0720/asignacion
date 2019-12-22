<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE lineas_detalle SET monto=".$_POST['monto']." WHERE idlinea =".$_POST['idfila'];
mysqli_query($con, $SQL);

include("reserva_altamodbaja_sol.php");
 mysqli_close($con);
?>