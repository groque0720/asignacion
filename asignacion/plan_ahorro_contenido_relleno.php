
<?php

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

include('plan_ahorro_contenido_relleno_total.php'); 
?>