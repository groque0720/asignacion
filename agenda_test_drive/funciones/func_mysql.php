<?php
	// 	//servidor
	// define('HOST','34.70.222.227');
	// //usuario bd
	// define('USER','remote_asignacion');
	// //pass
	// define('PASS','872');
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
    list($anio,$mes,$dia)=explode("-",$fecha);
    return $dia."-".$mes."-".$anio;
	};

	function cambiarFormatohora($hora){
    list($horas,$minutos,$segundos)=explode(":",$hora);
    return $horas.":".$minutos;
	};

		/** Actual month last day **/
	  function ultimo_dia_mes() {
	      $month = date('m');
	      $year = date('Y');
	      $day = date("d", mktime(0,0,0, $month+1, 0, $year));

	      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
	  };

	  /** Actual month first day **/
	  function primer_dia_mes() {
	      $month = date('m');
	      $year = date('Y');
	      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
	  }

?>