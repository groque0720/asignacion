<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	$SQL="SELECT * FROM registros WHERE siac = 1 AND usuario <> 0";
	$res = mysqli_query($con, $SQL);

	while ($reg=mysqli_fetch_array($res)) {

		$dia = $reg['dia'];
		$mes = $reg['mes'];
		$ano = $reg['ano'];

	    $fecha = strtotime($dia."-".$mes."-".$ano);
		$fecha = date('Y-m-d', $fecha);

		$idusuario = $reg['usuario'];
		$sector ='ventas';
		$cliente =$reg['Cliente'];
		$acercamiento = 'local';
		$interes = '';
		$telefono = $reg['telefono'];
		$asesor = $reg['asesor'];
		$seguimiento = '1';
		$email = $reg['email'];


		$SQL = "INSERT INTO regrecepcion (idusuario, fecha, sector, cliente, acercamiento, interes, telefono, asesor, email, seguimiento) values";
		$SQL .="( '$idusuario', '$fecha','$sector','$cliente','$acercamiento','$interes','$telefono','$asesor','$email','$seguimiento')";
		mysqli_query($con, $SQL);

	}

 ?>