<?php
	include("../config/config_mysql_asig.php");

	function conectar() {
		global $con;
		$con = mysql_connect(HOST,USER,PASS) or die("ERROR EN CONEXION:".mysql_error());
		$base_datos=mysql_select_db(DB, $con) or die("ERROR AL SELECCIONAR LA BASE DE DATOS:".mysql_error());
		//mysql_query("SET NAMES 'utf8'");
		return $con;
	};

	function cambiarFormatoFecha($fecha){
    list($anio,$mes,$dia)=explode("-",$fecha);
    return $dia."-".$mes."-".$anio;
	};

?>