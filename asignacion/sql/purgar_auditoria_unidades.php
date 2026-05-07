<?php
// =====================================================================
// Purga de auditoria_unidades.
// Borra el historial de unidades cuya fec_entrega tenga > 6 meses.
// =====================================================================
// Uso recomendado: programar como tarea diaria/semanal en el Programador
// de tareas de Windows ejecutando:
//   php c:\laragon\www\asignacion\asignacion\sql\purgar_auditoria_unidades.php
//
// O bien invocar por web (queda protegido por sesión + perfil 14):
//   /asignacion/sql/purgar_auditoria_unidades.php?web=1
//
// Salida: imprime cantidad de filas borradas y graba en api_log.txt.
// =====================================================================

include(__DIR__ . "/../funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

$es_cli = (php_sapi_name() === 'cli');

// Si vino por web exigimos sesión + perfil 14 (admin)
if (!$es_cli) {
	@session_start();
	if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] != "SI" || ((int)$_SESSION["idperfil"]) !== 14) {
		header("HTTP/1.1 403 Forbidden");
		echo "No autorizado.";
		exit();
	}
	if (!isset($_GET['web'])) {
		echo "Falta parámetro web=1.";
		exit();
	}
}

// Contar antes
$rs = mysqli_query($con, "
	SELECT COUNT(*) AS total
	FROM auditoria_unidades au
	JOIN asignaciones a ON a.id_unidad = au.id_unidad
	WHERE a.fec_entrega IS NOT NULL
	  AND a.fec_entrega <> '0000-00-00'
	  AND a.fec_entrega < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
");
$row = $rs ? mysqli_fetch_assoc($rs) : ['total' => 0];
$total = (int)$row['total'];

// Borrar
$ok = mysqli_query($con, "
	DELETE au FROM auditoria_unidades au
	JOIN asignaciones a ON a.id_unidad = au.id_unidad
	WHERE a.fec_entrega IS NOT NULL
	  AND a.fec_entrega <> '0000-00-00'
	  AND a.fec_entrega < DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
");
$borradas = $ok ? mysqli_affected_rows($con) : -1;

$msg = sprintf("[%s] purgar_auditoria_unidades — previstas:%d borradas:%d %s",
	date('Y-m-d H:i:s'),
	$total,
	$borradas,
	$ok ? 'OK' : ('ERROR: ' . mysqli_error($con))
);

@file_put_contents(__DIR__ . "/../api_log.txt", $msg . PHP_EOL, FILE_APPEND);

if ($es_cli) {
	echo $msg . PHP_EOL;
} else {
	echo "<pre>" . htmlspecialchars($msg) . "</pre>";
}
