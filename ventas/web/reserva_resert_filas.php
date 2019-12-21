<?php

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="DELETE FROM lineas_detalle WHERE idreserva= '".$_POST['nrores']."'";
mysqli_query($con, $SQL);

 include("reserva_altamodbaja_sol.php");
  mysqli_close($con);
 ?>

