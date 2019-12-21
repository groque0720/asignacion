<?php
		//servidor
	define('HOST','34.70.222.227');
	//usuario bd
	define('USER','remote_asignacion');
	//pass
	define('PASS','asignDyVSA2020');
	//base de datos
	define('DB','asignacion');

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

	function dias_transcurridos($fecha_i,$fecha_f)
	{
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);
		return $dias;
	}

?>