<?php
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login"); exit();
}
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) {
    header("Location: ../login"); exit();
}
$usuario = htmlspecialchars($_SESSION["usuario"] ?? '');
$anio_actual = intval(date('Y'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Encuesta Satisfacción 0km</title>
    <link rel="shortcut icon" type="image/x-icon" href="../asignacion/imagenes/favicon.ico">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Dashboard styles -->
    <link href="css/enc_dashboard.css" rel="stylesheet">
</head>
<body data-theme="dark">

<!-- ═══════════════ NAVBAR ═══════════════════════════════ -->
<nav class="dv-navbar">
    <a class="dv-navbar-brand" href="index.php">
        <i class="fa fa-arrow-left" style="font-size:14px;color:var(--text-muted)"></i>
    </a>
    <a class="dv-navbar-brand" href="resultado_dashboard.php">
        <i class="fa fa-chart-line"></i>
        <span>Encuesta Satisfacción — Dashboard</span>
    </a>
    <div class="dv-navbar-right">
        <div class="dv-user-badge">
            <i class="fa fa-user-circle"></i>
            <?= $usuario ?>
        </div>
        <button class="btn-icon" id="theme-toggle" title="Cambiar tema">
            <i class="fa fa-moon" id="theme-icon"></i>
        </button>
    </div>
</nav>

<!-- ═══════════════ MAIN ═════════════════════════════════ -->
<div class="dv-main">

    <!-- ── FILTROS ──────────────────────────────────────── -->
    <div id="filter-panel">
        <div class="filter-title"><i class="fa fa-filter"></i> Filtros</div>
        <form id="filter-form">
            <div class="row g-2 align-items-end">

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Año</label>
                    <select class="form-select form-select-sm" id="f-anio">
                        <option value="<?= $anio_actual ?>" selected><?= $anio_actual ?></option>
                        <option value="0">Todos los años</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Mes</label>
                    <select class="form-select form-select-sm" id="f-mes">
                        <option value="0">Todos</option>
                        <option value="1">Enero</option><option value="2">Febrero</option>
                        <option value="3">Marzo</option><option value="4">Abril</option>
                        <option value="5">Mayo</option><option value="6">Junio</option>
                        <option value="7">Julio</option><option value="8">Agosto</option>
                        <option value="9">Septiembre</option><option value="10">Octubre</option>
                        <option value="11">Noviembre</option><option value="12">Diciembre</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Desde</label>
                    <input type="date" class="form-control form-control-sm" id="f-fecha-desde">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control form-control-sm" id="f-fecha-hasta">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Sucursal</label>
                    <select class="form-select form-select-sm" id="f-sucursal">
                        <option value="0">Todas</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Asesor</label>
                    <select class="form-select form-select-sm" id="f-asesor">
                        <option value="0">Todos</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Grupo</label>
                    <select class="form-select form-select-sm" id="f-grupo">
                        <option value="0">Todos</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Versión</label>
                    <select class="form-select form-select-sm" id="f-modelo">
                        <option value="0">Todas</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Área</label>
                    <select class="form-select form-select-sm" id="f-area">
                        <option value="0">Todas</option>
                    </select>
                </div>

                <div class="col-12 col-md-auto d-flex align-items-end gap-2 mt-1">
                    <button type="button" class="btn-apply" id="btn-apply">
                        <i class="fa fa-search me-1"></i> Aplicar
                    </button>
                    <button type="button" class="btn-clear" id="btn-clear">
                        <i class="fa fa-times me-1"></i> Limpiar
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- ── KPI CARDS ────────────────────────────────────── -->
    <div id="kpi-row">

        <div class="kpi-card" id="kpi-promedio-card">
            <i class="fa fa-star kpi-icon"></i>
            <div class="kpi-label">Promedio General</div>
            <div class="kpi-value" id="kpi-promedio">—</div>
            <div class="kpi-sub">escala 0 – 10</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-clipboard-check kpi-icon"></i>
            <div class="kpi-label">Encuestas completadas</div>
            <div class="kpi-value" id="kpi-total">—</div>
            <div class="kpi-sub">respuestas recibidas</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-face-smile kpi-icon"></i>
            <div class="kpi-label">Alta satisfacción</div>
            <div class="kpi-value" id="kpi-exc">—</div>
            <div class="kpi-sub" id="kpi-exc-sub">encuestas ≥ 9</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-face-frown kpi-icon"></i>
            <div class="kpi-label">A mejorar</div>
            <div class="kpi-value" id="kpi-reg">—</div>
            <div class="kpi-sub" id="kpi-reg-sub">encuestas &lt; 7</div>
        </div>

    </div><!-- /kpi-row -->

    <!-- ── KPI ÁREAS (dinámicas) ────────────────────────── -->
    <div id="areas-section" style="display:none;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
                    color:var(--text-muted);margin-bottom:.6rem;">
            <i class="fa fa-layer-group" style="color:var(--accent-purple)"></i>
            Promedio por Área
        </div>
        <div id="areas-row"></div>
    </div>

    <!-- ── GRÁFICOS ──────────────────────────────────────── -->
    <div id="charts-grid">

        <!-- Fila 1: Tendencia + Distribución -->
        <div class="charts-row charts-row-2" style="margin-bottom:1rem;">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fa fa-chart-line"></i> Tendencia Mensual del Promedio
                </div>
                <canvas id="chart-tendencia"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fa fa-chart-pie" style="color:var(--accent-orange)"></i>
                    Distribución de Resultados
                </div>
                <canvas id="chart-dist"></canvas>
            </div>
        </div>

        <!-- Fila 2: Por sucursal + Top asesores -->
        <div class="charts-row charts-row-2" style="margin-bottom:1rem;">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fa fa-building" style="color:var(--accent-cyan)"></i>
                    Promedio por Sucursal
                </div>
                <canvas id="chart-sucursal"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fa fa-trophy" style="color:var(--accent-yellow)"></i>
                    Top Asesores por Promedio
                </div>
                <canvas id="chart-asesor"></canvas>
            </div>
        </div>

        <!-- Fila 3: Por área (ancho completo, dinámico) -->
        <div class="chart-card" style="margin-bottom:1rem;">
            <div class="chart-title">
                <i class="fa fa-layer-group" style="color:var(--accent-purple)"></i>
                Promedio por Área — todas las áreas
                <span style="font-size:10px;color:var(--text-muted);margin-left:6px;">(se actualiza automáticamente al agregar nuevas áreas)</span>
            </div>
            <canvas id="chart-areas" style="max-height:220px;"></canvas>
        </div>

    </div><!-- /charts-grid -->

    <!-- ── TABLA DETALLE ────────────────────────────────── -->
    <div id="table-section">
        <div class="table-header">
            <div class="table-title">
                <i class="fa fa-table"></i>
                Detalle de Encuestas Completadas
            </div>
        </div>
        <div class="table-responsive">
            <table id="main-table" class="table table-hover table-sm w-100">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Grupo</th>
                        <th>Versión</th>
                        <th>Asesor</th>
                        <th>Sucursal</th>
                        <th>Resultado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div><!-- /dv-main -->

<!-- ── LOADING ───────────────────────────────────────────── -->
<div id="loading-overlay">
    <div class="loading-box">
        <div class="spinner-border" role="status"></div>
        <p>Cargando datos...</p>
    </div>
</div>

<!-- ═══════════════ SCRIPTS ══════════════════════════════ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
</script>
<script src="js/enc_dashboard.js"></script>

</body>
</html>
