<?php
include("funciones/func_mysql.php");
conectar();
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}
$usuario = htmlspecialchars($_SESSION["usuario"] ?? '');
$anio_actual = intval(date('Y'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Ventas — DYV</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- DataTables + Bootstrap5 -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Dashboard styles -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body data-theme="dark">

<!-- ═══════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════ -->
<nav class="dv-navbar">
    <a class="dv-navbar-brand" href="index.php">
        <i class="fa fa-chart-line" style="color:var(--accent-blue);font-size:22px;"></i>
        <span>Informe de Ventas</span>
    </a>
    <div class="dv-navbar-right">
        <div class="dv-user-badge">
            <i class="fa fa-user-circle"></i>
            <?= $usuario ?>
        </div>
        <button class="btn-icon" id="theme-toggle" title="Cambiar tema">
            <i class="fa fa-moon" id="theme-icon"></i>
        </button>
        <a class="btn-icon" href="../login" title="Cerrar sesión">
            <i class="fa fa-sign-out-alt"></i>
        </a>
    </div>
</nav>

<!-- ═══════════════════════════════════════════════════════
     CONTENIDO PRINCIPAL
══════════════════════════════════════════════════════════ -->
<div class="dv-main">

    <!-- ─── PANEL DE FILTROS ──────────────────────────── -->
    <div id="filter-panel">
        <div class="filter-title"><i class="fa fa-filter"></i> Filtros</div>
        <form id="filter-form">
            <div class="row g-2 align-items-end">

                <!-- Fila 1 -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Año</label>
                    <select class="form-select form-select-sm" id="f-anio" name="anio">
                        <?php for ($y = $anio_actual; $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $anio_actual ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                        <option value="0">Todos los años</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Mes</label>
                    <select class="form-select form-select-sm" id="f-mes" name="mes">
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
                    <input type="date" class="form-control form-control-sm" id="f-fecha-desde" name="fecha_desde">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" class="form-control form-control-sm" id="f-fecha-hasta" name="fecha_hasta">
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Sucursal</label>
                    <select class="form-select form-select-sm" id="f-sucursal" name="idsucursal">
                        <option value="0">Todas</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Vendedor</label>
                    <select class="form-select form-select-sm" id="f-vendedor" name="idusuario">
                        <option value="0">Todos</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Modelos</label>
                    <select class="form-select form-select-sm" id="f-grupo" name="idgrupo">
                        <option value="0">Todos</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Versiones</label>
                    <select class="form-select form-select-sm" id="f-modelo" name="idmodelo">
                        <option value="0">Todas</option>
                    </select>
                </div>


                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Anuladas</label>
                    <select class="form-select form-select-sm" id="f-anulada" name="anulada">
                        <option value="-1">Todas</option>
                        <option value="0">No anuladas</option>
                        <option value="1">Anuladas</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Crédito</label>
                    <select class="form-select form-select-sm" id="f-credito" name="credito">
                        <option value="-1">Todos</option>
                        <option value="1">Con crédito</option>
                        <option value="0">Contado</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Toma Usado</label>
                    <select class="form-select form-select-sm" id="f-toma" name="toma_usado">
                        <option value="-1">Todos</option>
                        <option value="1">Con toma</option>
                        <option value="0">Sin toma</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label">Compra</label>
                    <select class="form-select form-select-sm" id="f-compra" name="compra">
                        <option value="">Todos</option>
                        <option value="nuevo">Nuevo</option>
                        <option value="usado">Usado</option>
                    </select>
                </div>

                <!-- Botones y toggle -->
                <div class="col-12 col-md-auto d-flex align-items-end gap-2 flex-wrap mt-1">
                    <button type="button" class="btn-apply" id="btn-apply">
                        <i class="fa fa-search me-1"></i> Aplicar
                    </button>
                    <button type="button" class="btn-clear" id="btn-clear">
                        <i class="fa fa-times me-1"></i> Limpiar
                    </button>
                    <label class="comparison-toggle">
                        <input type="checkbox" id="toggle-comparison">
                        <i class="fa fa-code-compare" style="color:var(--accent-purple)"></i>
                        Comparar año anterior
                    </label>
                </div>

            </div><!-- /row -->
        </form>
    </div><!-- /filter-panel -->

    <!-- ─── KPI CARDS ─────────────────────────────────── -->
    <div id="kpi-row">

        <div class="kpi-card">
            <i class="fa fa-car kpi-icon"></i>
            <div class="kpi-label">Total Reservas</div>
            <div class="kpi-value" id="kpi-total">—</div>
            <div class="kpi-sub">unidades</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-star kpi-icon"></i>
            <div class="kpi-label">Nuevas</div>
            <div class="kpi-value" id="kpi-nuevas">—</div>
            <div class="kpi-sub" id="kpi-nuevas-sub">unidades nuevas</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-rotate-left kpi-icon"></i>
            <div class="kpi-label">Usadas</div>
            <div class="kpi-value" id="kpi-usadas">—</div>
            <div class="kpi-sub" id="kpi-usadas-sub">unidades usadas</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-ban kpi-icon"></i>
            <div class="kpi-label">Anuladas</div>
            <div class="kpi-value" id="kpi-anuladas">—</div>
            <div class="kpi-sub" id="kpi-anuladas-sub">del total</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-percent kpi-icon"></i>
            <div class="kpi-label">% Anuladas</div>
            <div class="kpi-value" id="kpi-pct-anuladas">—</div>
            <div class="kpi-sub">tasa de anulación</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-credit-card kpi-icon"></i>
            <div class="kpi-label">Con Crédito</div>
            <div class="kpi-value" id="kpi-credito">—</div>
            <div class="kpi-sub" id="kpi-credito-sub">financiadas</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-percent kpi-icon"></i>
            <div class="kpi-label">% Crédito</div>
            <div class="kpi-value" id="kpi-pct-credito">—</div>
            <div class="kpi-sub">financiamiento</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-exchange-alt kpi-icon"></i>
            <div class="kpi-label">Toma Usado</div>
            <div class="kpi-value" id="kpi-toma">—</div>
            <div class="kpi-sub" id="kpi-toma-sub">con toma</div>
        </div>

        <div class="kpi-card">
            <i class="fa fa-percent kpi-icon"></i>
            <div class="kpi-label">% Toma Usado</div>
            <div class="kpi-value" id="kpi-pct-toma">—</div>
            <div class="kpi-sub">usados tomados</div>
        </div>

    </div><!-- /kpi-row -->

    <!-- ─── GRÁFICOS ───────────────────────────────────── -->
    <div id="charts-grid">

        <!-- Fila 1: Reservas por mes / Comparación anual -->
        <div class="charts-row charts-row-2">
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-chart-bar"></i> Reservas por Mes</div>
                <canvas id="chart-mes"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-chart-line"></i> Comparación Interanual (3 años)</div>
                <canvas id="chart-anio-comp"></canvas>
            </div>
        </div>

        <!-- Tabla comparativa interanual -->
        <div class="chart-card" style="margin-bottom:1rem;">
            <div class="chart-title" style="justify-content:space-between;">
                <span><i class="fa fa-table-columns" style="color:var(--accent-purple)"></i> Comparación Mes a Mes — 3 Años</span>
                <button onclick="this.closest('.chart-card').querySelector('#comp-table-wrap').classList.toggle('d-none')"
                        style="background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-secondary);border-radius:6px;padding:3px 10px;cursor:pointer;font-size:11px;">
                    Mostrar / Ocultar
                </button>
            </div>
            <div id="comp-table-wrap" class="d-none" style="overflow-x:auto;margin-top:4px;">
                <table id="comp-table" style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead id="comp-table-head"></thead>
                    <tbody id="comp-table-body"></tbody>
                </table>
            </div>

            <!-- Comparación por Grupo -->
            <div id="comp-grupo-wrap" class="d-none" style="overflow-x:auto;margin-top:14px;">
                <div style="font-size:12px;font-weight:700;color:var(--text-primary);margin-bottom:6px;letter-spacing:.5px;">
                    <i class="fa fa-layer-group" style="color:var(--accent-purple);margin-right:6px;"></i>
                    Comparación por Grupo — 3 Años
                </div>
                <table id="comp-grupo-table" style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead id="comp-grupo-head"></thead>
                    <tbody id="comp-grupo-body"></tbody>
                </table>
            </div>
        </div>

        <!-- Comparativo mes a mes por modelo/versión -->
        <div class="chart-card" style="margin-bottom:1rem;">
            <div class="chart-title" style="justify-content:space-between;flex-wrap:wrap;gap:6px;">
                <span><i class="fa fa-arrows-left-right" style="color:var(--accent-cyan)"></i> Comparativo Mes a Mes — Por Modelo/Versión</span>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    <input type="text" id="modelo-mes-search" placeholder="Buscar grupo/versión..." autocomplete="off"
                        style="background:var(--bg-primary);border:1px solid var(--border-color);color:var(--text-primary);border-radius:6px;padding:3px 10px;font-size:11px;width:170px;outline:none;">
                    <button id="btn-expand-all" onclick="modeloMesExpandAll(true)"
                            style="background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-secondary);border-radius:6px;padding:3px 10px;cursor:pointer;font-size:11px;">
                        <i class="fa fa-angles-down"></i> Expandir todo
                    </button>
                    <button id="btn-collapse-all" onclick="modeloMesExpandAll(false)"
                            style="background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-secondary);border-radius:6px;padding:3px 10px;cursor:pointer;font-size:11px;">
                        <i class="fa fa-angles-up"></i> Colapsar todo
                    </button>
                    <button onclick="this.closest('.chart-card').querySelector('#modelo-mes-wrap').classList.toggle('d-none')"
                            style="background:var(--bg-secondary);border:1px solid var(--border-color);color:var(--text-secondary);border-radius:6px;padding:3px 10px;cursor:pointer;font-size:11px;">
                        Mostrar / Ocultar
                    </button>
                </div>
            </div>
            <div id="modelo-mes-wrap" style="overflow-x:auto;margin-top:6px;">
                <table id="modelo-mes-table" style="width:100%;border-collapse:collapse;font-size:11px;">
                    <thead id="modelo-mes-thead"></thead>
                    <tbody id="modelo-mes-tbody"></tbody>
                </table>
                <div id="modelo-mes-empty" style="display:none;text-align:center;padding:18px;color:var(--text-muted);font-size:12px;">
                    <i class="fa fa-circle-info"></i> Sin datos para el período seleccionado.
                </div>
            </div>
        </div>

        <!-- Fila 2: Por sucursal / Vendedores / Anuladas -->
        <div class="charts-row charts-row-3">
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-building"></i> Por Sucursal</div>
                <canvas id="chart-sucursal"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-trophy"></i> Top 10 Vendedores</div>
                <canvas id="chart-vendedor"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-circle-xmark"></i> Anuladas vs Activas</div>
                <canvas id="chart-anuladas"></canvas>
            </div>
        </div>

        <!-- Fila 3: Crédito / Toma usado / Compra -->
        <div class="charts-row charts-row-3">
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-credit-card"></i> Crédito vs Contado</div>
                <canvas id="chart-credito"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-exchange-alt"></i> Toma Usado vs Sin Toma</div>
                <canvas id="chart-toma"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="fa fa-tag"></i> Compra: Nuevo vs Usado</div>
                <canvas id="chart-compra"></canvas>
            </div>
        </div>

    </div><!-- /charts-grid -->

    <!-- ─── TABLA DE DATOS ─────────────────────────────── -->
    <div id="table-section">
        <div class="table-section-header">
            <div class="table-section-title">
                <i class="fa fa-table" style="color:var(--accent-blue)"></i>
                Detalle de Reservas
            </div>
            <div class="export-buttons">
                <button class="btn-export btn-excel" onclick="exportExcel()">
                    <i class="fa fa-file-excel"></i> Excel
                </button>
                <button class="btn-export btn-pdf" onclick="exportPDF()">
                    <i class="fa fa-file-pdf"></i> PDF
                </button>
                <button class="btn-export btn-report" onclick="openReport()">
                    <i class="fa fa-print"></i> Reporte Gerencia
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="main-table" class="table table-hover table-sm w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Sucursal</th>
                        <th>Vendedor</th>
                        <th>Modelo-Versión</th>
                        <th>Compra</th>
                        <th>Anulada</th>
                        <th>Crédito</th>
                        <th>Toma Usado</th>
                        <th><i class="fa fa-eye"></i></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div><!-- /table-section -->

</div><!-- /dv-main -->

<!-- ─── LOADING OVERLAY ───────────────────────────── -->
<div id="loading-overlay">
    <div class="loading-box">
        <div class="spinner-border" role="status"></div>
        <p>Cargando datos...</p>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════════════ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="js/dashboard.js"></script>

</body>
</html>
