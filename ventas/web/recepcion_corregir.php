<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");


$SQL = "UPDATE regrecepcion SET ";
$SQL .="sector ='Ventas', ";
$SQL .="acercamiento = 'Presencial', ";
$SQL .="email ='.' ";
$SQL .=" WHERE idusuario = 57 AND idcontacto > 631";

mysqli_query($con, $SQL);




 ?>