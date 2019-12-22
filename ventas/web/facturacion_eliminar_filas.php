<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="DELETE  FROM facturas_lineas WHERE idlinea =".$_POST['idfila'];
mysqli_query($con, $SQL); ?>

<?php include("facturacion_altamodbaja_sol.php");
 mysqli_close($con);  ?>

