<?php

	include($_SERVER['DOCUMENT_ROOT']."/config/config_mysql.php");

	date_default_timezone_set("America/Argentina/Buenos_Aires");

	function conectar() {
		global $con;
		$con = mysqli_connect(HOST, USER, PASS) or die("ERROR EN CONEXION: " . mysqli_error());
		$base_datos = mysqli_select_db($con, DB) or die("ERROR AL SELECCIONAR LA BASE DE DATOS: " . mysqli_error());
		mysqli_query($con, "SET NAMES 'utf8'");
		return $con;
	}

	function cambiarFormatoFecha($fecha) {
		if ($fecha != '' && $fecha != null && $fecha != '0000-00-00') {
			list($anio, $mes, $dia) = explode("-", $fecha);
			return $dia . "-" . $mes . "-" . substr($anio, 2);
		} else {
			return '-';
		}
	}

	function fechaLarga($fecha) {
		if ($fecha == '' || $fecha == null || $fecha == '0000-00-00') return '-';
		$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
		          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		list($anio, $mes, $dia) = explode("-", $fecha);
		return (int)$dia . " de " . $meses[(int)$mes] . " de " . $anio;
	}

	// Genera un token único de 64 caracteres (SHA-256)
	function generarToken($id_asignacion) {
		return hash('sha256', $id_asignacion . microtime(true) . random_bytes(16));
	}

	// Evalúa la condición de una pregunta contra las respuestas ya dadas
	// $valor_respuesta: valor numérico de la pregunta de referencia
	// $operador: '<','<=','=','>=','>','!='
	// $cond_valor: string del valor comparado
	function evaluarCondicion($valor_respuesta, $operador, $cond_valor) {
		$v = (float)$valor_respuesta;
		$c = (float)$cond_valor;
		switch ($operador) {
			case '<':  return $v <  $c;
			case '<=': return $v <= $c;
			case '=':  return $v == $c;
			case '>=': return $v >= $c;
			case '>':  return $v >  $c;
			case '!=': return $v != $c;
			default:   return true;
		}
	}

?>
