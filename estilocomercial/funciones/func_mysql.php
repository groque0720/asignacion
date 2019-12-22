<?php
	// //servidor
	// define('HOST','34.70.222.227');
	// //usuario bd
	// define('USER','remote_asignacion');
	// //pass
	// define('PASS','872');
	// //base de datos
	// define('DB','sysdyv');
	include($_SERVER['DOCUMENT_ROOT']."/config/config_mysql.php");
	date_default_timezone_set("America/Argentina/Buenos_Aires");

	function conectar() {
		global $con;
		$con = mysql_connect(HOST,USER,PASS) or die("ERROR EN CONEXION:".mysql_error());
		$base_datos=mysql_select_db(DB, $con) or die("ERROR AL SELECCIONAR LA BASE DE DATOS:".mysql_error());
		//mysql_query("SET NAMES 'utf8'");
		return $con;
	};

	function cambiarFormatoFecha($fecha){
		if (is_null($fecha)) {
			return "";
		}else{
		    list($anio,$mes,$dia)=explode("-",$fecha);
		    return $dia."-".$mes."-".$anio;
		}

	};

	function cambiarFormatohora($hora){
    list($horas,$minutos,$segundos)=explode(":",$hora);
    return $horas.":".$minutos;
	};

?>