<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");


$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 19";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 20";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 21";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 22";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 23";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 24";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 25";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 26";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 27";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 28";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 70";
mysqli_query($con, $SQL);
$SQL="UPDATE reservas SET idgrupo = 15 WHERE idmodelo= 71";
mysqli_query($con, $SQL);
 ?>