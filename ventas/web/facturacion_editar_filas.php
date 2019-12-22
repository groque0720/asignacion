<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");



$SQL="UPDATE facturas_lineas SET monto=".$_POST['monto']." WHERE idlinea =".$_POST['idfila'];
mysqli_query($con, $SQL);

?>

<?php include("facturacion_altamodbaja_sol.php");
 mysqli_close($con);  ?>