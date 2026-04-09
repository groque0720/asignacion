<?php
/**
 * _init.php — Inicialización común del módulo usados_docs
 * Conexión BD, verificación de sesión, creación de tablas, definiciones globales.
 */

include_once($_SERVER['DOCUMENT_ROOT'] . "/asignacion/funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
@session_start();

if ($_SESSION["autentificado"] != "SI") {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'No autenticado']);
    } else {
        header("Location: ../login");
    }
    exit();
}

$id_usuario     = (int)$_SESSION["id"];
$id_perfil      = (int)$_SESSION["idperfil"];
$nombre_usuario = $_SESSION["usuario"];
$es_admin       = in_array($id_perfil, [1, 2]);

// ── Crear tablas si no existen ──────────────────────────────────────────────

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_items` (
  `id_item`     int(11)      NOT NULL AUTO_INCREMENT,
  `nombre`      varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo`      tinyint(1)   NOT NULL DEFAULT 1,
  `posicion`    int(11)      NOT NULL DEFAULT 0,
  `created_at`  timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_seguimiento` (
  `id`          int(11)    NOT NULL AUTO_INCREMENT,
  `id_unidad`   int(11)    NOT NULL,
  `id_item`     int(11)    NOT NULL,
  `estado`      tinyint(1) NOT NULL DEFAULT 0,
  `id_usuario`  int(11)    DEFAULT NULL,
  `observacion` text       DEFAULT NULL,
  `archivo`     varchar(255) DEFAULT NULL,
  `updated_at`  timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_unidad_item` (`id_unidad`, `id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

mysqli_query($con, "CREATE TABLE IF NOT EXISTS `usados_docs_historial` (
  `id`               int(11)    NOT NULL AUTO_INCREMENT,
  `id_unidad`        int(11)    NOT NULL,
  `id_item`          int(11)    NOT NULL,
  `estado_anterior`  tinyint(1) DEFAULT NULL,
  `estado_nuevo`     tinyint(1) NOT NULL,
  `id_usuario`       int(11)    NOT NULL,
  `fecha`            datetime   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacion`      text       DEFAULT NULL,
  `archivo`          varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unidad_item` (`id_unidad`, `id_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

// ── Ítems iniciales (solo si la tabla está vacía) ───────────────────────────

$rc = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM usados_docs_items"));
if ((int)$rc['c'] === 0) {
    mysqli_query($con, "INSERT INTO `usados_docs_items` (`nombre`, `descripcion`, `posicion`) VALUES
        ('Fotos cargadas',   'Fotos del vehículo cargadas en el sistema', 1),
        ('Fotos editadas',   'Fotos procesadas y editadas',               2),
        ('Peritaje',         'Informe de peritaje del vehículo',          3),
        ('Check 150 puntos', 'Revisión de 150 puntos completada',         4)");
}

// ── Definición de estados ───────────────────────────────────────────────────
// 0 = Pendiente | 1 = Hecho | 2 = No corresponde | 3 = En proceso

$ESTADOS = [
    0 => ['label' => 'Pendiente',       'icon' => '○', 'class' => 'est-pendiente'],
    1 => ['label' => 'Hecho',           'icon' => '✓', 'class' => 'est-hecho'],
    2 => ['label' => 'No corresponde',  'icon' => '−', 'class' => 'est-no-corresponde'],
    3 => ['label' => 'En proceso',      'icon' => '◑', 'class' => 'est-en-proceso'],
];

/**
 * Calcula el estado general de un usado en base a sus ítems.
 * No corresponde (2) no afecta el estado general.
 *
 * @return int  0 = Pendiente | 1 = Completo | 3 = En proceso
 */
function calcular_estado_general(int $id_unidad, array $items, array $seguimiento): int
{
    $hay_pendiente  = false;
    $hay_en_proceso = false;
    foreach ($items as $item) {
        $s = $seguimiento[$id_unidad][$item['id_item']] ?? null;
        $e = $s ? (int)$s['estado'] : 0;
        if ($e === 0) $hay_pendiente  = true;
        if ($e === 3) $hay_en_proceso = true;
    }
    if ($hay_pendiente)  return 0;
    if ($hay_en_proceso) return 3;
    return 1;
}
