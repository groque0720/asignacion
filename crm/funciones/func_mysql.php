<?php

	// 	//servidor
	// define('HOST','mysql.hostinger.com.ar');
	// //usuario bd
	// define('USER','u190127179_tpa');
	// //pass
	// define('PASS','derkayvargas');
	// //base de datos
	// define('DB','u190127179_tpa');



	include($_SERVER['DOCUMENT_ROOT']."/config/config_mysql.php");
	date_default_timezone_set("America/Argentina/Buenos_Aires");

	function conectar() {
		global $con;
		$con = mysqli_connect(HOST,USER,PASS) or die("ERROR EN CONEXION:".mysqli_error());
		$base_datos=mysqli_select_db($con, DB) or die("ERROR AL SELECCIONAR LA BASE DE DATOS:".mysqli_error());
		mysqli_query($con, "SET NAMES 'utf8'");
		return $con;
	};

	function cambiarFormatoFecha($fecha){
		if ($fecha!='') {
			list($anio,$mes,$dia)=explode("-",$fecha);
    		return $dia."-".$mes."-".substr($anio,2);
		}else{
			return '-';
		}
 	};

	function cambiarFormatohora($hora){
    list($horas,$minutos,$segundos)=explode(":",$hora);
    return $horas.":".$minutos;
	};

?>