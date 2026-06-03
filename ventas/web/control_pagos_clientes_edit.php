<?php
 include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

// Fecha vacía / "null" / 0  =>  NULL real.
// Con sql_mode estricto (STRICT_TRANS_TABLES/NO_ZERO_DATE) una cadena '' en columna DATE
// dispara error 1292 y aborta TODO el UPDATE (en prod, sin modo estricto, se guarda igual).
function fechaSQL($con, $v){
	$v = trim((string)$v);
	if ($v === '' || strtolower($v) === 'null' || $v === '0') { return "NULL"; }
	return "'".mysqli_real_escape_string($con, $v)."'";
}

$SQL="UPDATE reservas SET";
$SQL .="  interno = '".$_POST["nroint"]."', ";
$SQL .="  nrounidad = '".$_POST["nrou"]."', ";
$SQL .="  llego=".fechaSQL($con, $_POST["fecarr"] ?? '').", ";
$SQL .="  fechacanc=".fechaSQL($con, $_POST["feccan"] ?? '').", ";
$SQL .="  fechaentrega=".fechaSQL($con, $_POST["fecent"] ?? '').", ";
$SQL .="  nroorden='".$_POST["no"]."', ";
$SQL .="  obscanc ='".$_POST["obs"]."' ";
$SQL .=" WHERE idreserva = '".$_POST["id"]."'";
if (!mysqli_query($con, $SQL)) {
	http_response_code(500);
	echo "ERROR: ".mysqli_error($con);
}
 mysqli_close($con);
?>