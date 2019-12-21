<?php

include ("../includes/security.php");

include("../funciones/func_mysql.php");
conectar();

$SQL="UPDATE codigos SET ";
$SQL .= " detalle = '".$_POST["detalle"]."', ";
$SQL .= " movimiento = '".($_POST["movimiento"]+0)."', ";
$SQL .= " descuento = '".($_POST["esdescuento"]+0)."', ";
$SQL .= " credito = '".($_POST["escredito"]+0)."', ";
$SQL .= " tipocredito = '".($_POST["tipocredito"]+0)."', ";
$SQL .= " financiera = '".($_POST["financiera"]+0)."' ";
$SQL .= " WHERE idcodigo =".$_POST["idcodigo"];

//echo $SQL;

mysqli_query($con, $SQL);
header("Location: codigos.php");
// ?>