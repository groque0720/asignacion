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

		// Variables de sesión MySQL leídas por trigger trg_asignaciones_audit_update
		// (tabla auditoria_unidades). Si no hay sesión PHP cae a 0 / 'sistema'.
		if (session_status() === PHP_SESSION_NONE) {
			@session_start();
		}
		$uid    = isset($_SESSION['id']) ? (int)$_SESSION['id'] : 0;
		$uname  = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'sistema';
		$origen = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : 'cli';
		$uname  = mysqli_real_escape_string($con, $uname);
		$origen = mysqli_real_escape_string($con, $origen);
		mysqli_query($con, "SET @id_usuario = $uid, @usuario_nombre = '$uname', @origen = '$origen'");

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