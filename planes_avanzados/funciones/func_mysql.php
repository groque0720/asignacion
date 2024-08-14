<?php
	// 	//servidor
	// define('HOST','34.70.222.227');
	// //usuario bd
	// define('USER','remote_asignacion');
	// //pass
	// define('PASS','asignDyVSA2020');
	// //base de datos
	// define('DB','asignacion');

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

 	function nombre_del_mes($mes=''){

 		if ($mes == '') {
 			$mes = date('m');
 		}
 		switch ($mes) {
 			case 1: return 'Enero'; break;
 			case 2: return 'Febrero';break;
 			case 3: return 'Marzo';break;
 			case 4: return 'Abril';break;
 			case 5: return 'Mayo';break;
 			case 6: return 'Junio';break;
 			case 7: return 'Julio';break;
 			case 8: return 'Agosto';break;
 			case 9: return 'Septiembre';break;
 			case 10: return 'Octubre';break;
 			case 11: return 'Noviembre';break;
 			case 12: return 'Diciembre';break;
 			default:
 				return 'no existe mes';
 		}
 	}

	function dias_transcurridos($fecha_i,$fecha_f)
	{
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);
		return $dias;
	}

?>