<?php
	//servidor
	define('HOST','localhost');
	//usuario bd
	define('USER','root');
	//pass
	define('PASS','');
	//base de datos
	define('DB','sysdyv');


	// define('HOST','localhost');
	// //usuario bd
	// define('USER','remote_asignacion');
	// //pass
	// define('PASS','asignDyVSA2020');
	// //base de datos
	// define('DB','sysdyv');



	//include($_SERVER['DOCUMENT_ROOT']."/config/config_mysql.php");
	date_default_timezone_set("America/Argentina/Buenos_Aires");

	function conectar() {
		global $con;
		$con = mysqli_connect(HOST,USER,PASS) or die("ERROR EN CONEXION:".mysqli_error());
		$base_datos=mysqli_select_db($con, DB) or die("ERROR AL SELECCIONAR LA BASE DE DATOS:".mysqli_error());
		mysqli_query($con, "SET NAMES 'utf8'");
		return $con;
	};

	function cambiarFormatoFecha($fecha){
		if ($fecha!='' AND $fecha != null) {
			list($anio,$mes,$dia)=explode("-",$fecha);
    		return $dia."-".$mes."-".substr($anio,2);
		}else{
			return '-';
		}
	};

	function cambiarFormatohora($hora){
		if ($hora != null AND $hora != '') {
			list($horas,$minutos,$segundos)=explode(":",$hora);
    		return $horas.":".$minutos;
		}else{
			return '-';
		}
 	};

	function dias_transcurridos($fecha_i,$fecha_f)
	{
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);
		return $dias;
	}

?>