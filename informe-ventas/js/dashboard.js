/**
 * dashboard.js — Dashboard BI de Ventas
 * Maneja filtros, Chart.js, DataTables y exportaciones
 */

'use strict';

// ══════════════════════════════════════════════════════════
// ESTADO GLOBAL
// ══════════════════════════════════════════════════════════
const DashboardState = {
    filters: {
        anio:         new Date().getFullYear(),
        mes:          0,
        fecha_desde:  '',
        fecha_hasta:  '',
        idsucursal:   0,
        idusuario:    0,
        idgrupo:      0,
        idmodelo:     0,
        anulada:      -1,
        credito:      -1,
        toma_usado:   -1,
        compra:       ''
    },
    comparisonMode: false,
    charts: {},
    dtTable: null
};

// Registrar plugin de datalabels (requiere CDN cargado antes)
if (typeof ChartDataLabels !== 'undefined') {
    Chart.register(ChartDataLabels);
}

// Labels en español para Chart.js
const MONTH_LABELS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
const MONTH_FULL   = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

// Paleta de colores BI
const COLORS = {
    blue:   '#4e9af1',
    green:  '#63c795',
    orange: '#f1a84e',
    red:    '#e05c5c',
    purple: '#a78bfa',
    cyan:   '#22d3ee',
    yellow: '#fbbf24',
    pie: ['#4e9af1','#63c795','#f1a84e','#e05c5c','#a78bfa','#22d3ee','#fbbf24']
};

// ══════════════════════════════════════════════════════════
// INICIALIZACIÓN
// ══════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function() {
    restoreTheme();
    loadFilters();
    initCharts();
    initDataTable();
    bindFilterEvents();
    applyFilters();
});

// ══════════════════════════════════════════════════════════
// TEMA CLARO / OSCURO
// ══════════════════════════════════════════════════════════
function restoreTheme() {
    const saved = localStorage.getItem('dv-theme') || 'dark';
    document.body.dataset.theme = saved;
    updateThemeIcon(saved);
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('theme-icon');
    if (!icon) return;
    icon.className = theme === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
}

document.getElementById('theme-toggle').addEventListener('click', function() {
    const current = document.body.dataset.theme;
    const next = current === 'dark' ? 'light' : 'dark';
    document.body.dataset.theme = next;
    localStorage.setItem('dv-theme', next);
    updateThemeIcon(next);
    updateChartsTheme(next);
});

function updateChartsTheme(theme) {
    const textColor   = theme === 'dark' ? '#9fa8c0' : '#4a5568';
    const gridColor   = theme === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.08)';
    Object.values(DashboardState.charts).forEach(chart => {
        if (!chart) return;
        if (chart.options.plugins && chart.options.plugins.legend) {
            chart.options.plugins.legend.labels.color = textColor;
        }
        if (chart.options.scales) {
            Object.values(chart.options.scales).forEach(axis => {
                if (axis.ticks) axis.ticks.color = textColor;
                if (axis.grid)  axis.grid.color  = gridColor;
            });
        }
        chart.update();
    });
}

// ══════════════════════════════════════════════════════════
// CARGAR FILTROS (dropdowns)
// ══════════════════════════════════════════════════════════
async function loadFilters() {
    try {
        const res  = await fetch('api.php?action=filters');
        const data = await res.json();

        populateSelect('f-sucursal', data.sucursales || []);
        populateSelect('f-vendedor', data.vendedores || []);
        populateSelect('f-grupo',    data.grupos     || []);
        // Versiones se cargan dinámicamente al cambiar Modelos
        loadVersionesByGrupo(0);


        // Completar años disponibles si hay más
        if (data.anios && data.anios.length > 0) {
            const sel = document.getElementById('f-anio');
            if (sel) {
                const existingYears = Array.from(sel.options).map(o => parseInt(o.value));
                data.anios.forEach(y => {
                    if (!existingYears.includes(y)) {
                        const opt = document.createElement('option');
                        opt.value = y;
                        opt.textContent = y;
                        sel.insertBefore(opt, sel.lastElementChild);
                    }
                });
            }
        }
    } catch(e) {
        console.error('Error cargando filtros:', e);
    }
}

function populateSelect(id, items) {
    const sel = document.getElementById(id);
    if (!sel) return;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = item.label;
        sel.appendChild(opt);
    });
}

async function loadVersionesByGrupo(idgrupo) {
    const sel = document.getElementById('f-modelo');
    if (!sel) return;
    const current = sel.value;
    sel.innerHTML = '<option value="0">Todas</option>';
    try {
        const res  = await fetch('api.php?action=modelos_by_grupo&idgrupo=' + intval(idgrupo));
        const data = await res.json();
        (data || []).forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.label;
            sel.appendChild(opt);
        });
        // Restaurar selección previa si aún existe
        if (current && sel.querySelector('option[value="' + current + '"]')) {
            sel.value = current;
        }
    } catch(e) { console.error('Error cargando versiones:', e); }
}

function intval(v) { return parseInt(v) || 0; }

// ══════════════════════════════════════════════════════════
// INICIALIZAR CHARTS
// ══════════════════════════════════════════════════════════
function chartDefaults() {
    return {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: { color: '#9fa8c0', font: { size: 11 } }
            },
            tooltip: {
                backgroundColor: '#1e2235',
                titleColor: '#e8eaf6',
                bodyColor: '#9fa8c0',
                borderColor: '#2d3150',
                borderWidth: 1
            },
            datalabels: { display: false }  // desactivado por defecto
        }
    };
}

// Datalabels para barras: muestra valor encima de cada barra
function dlBar() {
    return {
        display: true,
        anchor: 'end',
        align: 'top',
        clamp: true,
        formatter: function(v) { return v > 0 ? v.toLocaleString('es-AR') : ''; },
        color: '#9fa8c0',
        font: { size: 10, weight: '600' }
    };
}

// Datalabels para pie/doughnut: muestra N + %
function dlPie() {
    return {
        display: true,
        formatter: function(value, ctx) {
            const total = ctx.dataset.data.reduce(function(a, b) { return a + (b || 0); }, 0);
            const pct = total > 0 ? Math.round(value / total * 100) : 0;
            return pct >= 4 ? value.toLocaleString('es-AR') + '\n' + pct + '%' : '';
        },
        color: '#ffffff',
        font: { size: 10, weight: 'bold' },
        textAlign: 'center'
    };
}

function scaleDefaults() {
    return {
        ticks:  { color: '#9fa8c0', font: { size: 11 } },
        grid:   { color: 'rgba(255,255,255,0.05)' },
        border: { color: 'rgba(255,255,255,0.08)' }
    };
}

function initCharts() {
    // 1. Reservas por mes (bar)
    DashboardState.charts.mes = new Chart(
        document.getElementById('chart-mes').getContext('2d'), {
        type: 'bar',
        data: {
            labels: MONTH_LABELS,
            datasets: [{
                label: 'Reservas',
                data: Array(12).fill(0),
                backgroundColor: COLORS.blue + 'bb',
                borderColor: COLORS.blue,
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            ...chartDefaults(),
            layout: { padding: { top: 18 } },
            plugins: { ...chartDefaults().plugins, legend: { display: false }, datalabels: dlBar() },
            scales: { x: scaleDefaults(), y: { ...scaleDefaults(), beginAtZero: true } }
        }
    });

    // 2. Comparación 3 años (line)
    DashboardState.charts.anioComp = new Chart(
        document.getElementById('chart-anio-comp').getContext('2d'), {
        type: 'line',
        data: {
            labels: MONTH_LABELS,
            datasets: [
                {
                    label: String(new Date().getFullYear()),
                    data: Array(12).fill(0),
                    borderColor: COLORS.blue,
                    backgroundColor: COLORS.blue + '22',
                    tension: 0.35, fill: true,
                    pointRadius: 3, pointHoverRadius: 5
                },
                {
                    label: String(new Date().getFullYear() - 1),
                    data: Array(12).fill(0),
                    borderColor: COLORS.orange,
                    backgroundColor: COLORS.orange + '22',
                    tension: 0.35, fill: true,
                    pointRadius: 3, pointHoverRadius: 5,
                    borderDash: [5, 3],
                    hidden: true
                },
                {
                    label: String(new Date().getFullYear() - 2),
                    data: Array(12).fill(0),
                    borderColor: COLORS.green,
                    backgroundColor: COLORS.green + '22',
                    tension: 0.35, fill: true,
                    pointRadius: 3, pointHoverRadius: 5,
                    borderDash: [2, 4],
                    hidden: true
                }
            ]
        },
        options: {
            ...chartDefaults(),
            // datalabels desactivado por defecto (heredado de chartDefaults)
            scales: { x: scaleDefaults(), y: { ...scaleDefaults(), beginAtZero: true } }
        }
    });

    // 3. Por sucursal (horizontal bar)
    DashboardState.charts.sucursal = new Chart(
        document.getElementById('chart-sucursal').getContext('2d'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Reservas',
                data: [],
                backgroundColor: COLORS.green + 'bb',
                borderColor: COLORS.green,
                borderWidth: 1,
                borderRadius: 3
            }]
        },
        options: {
            ...chartDefaults(),
            indexAxis: 'y',
            layout: { padding: { right: 38 } },
            plugins: { ...chartDefaults().plugins, legend: { display: false },
                datalabels: { ...dlBar(), anchor: 'end', align: 'right', color: '#9fa8c0' } },
            scales: {
                x: { ...scaleDefaults(), beginAtZero: true },
                y: { ...scaleDefaults(), ticks: { ...scaleDefaults().ticks, font: { size: 10 } } }
            }
        }
    });

    // 4. Top 10 Vendedores (horizontal bar)
    DashboardState.charts.vendedor = new Chart(
        document.getElementById('chart-vendedor').getContext('2d'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Reservas',
                data: [],
                backgroundColor: COLORS.purple + 'bb',
                borderColor: COLORS.purple,
                borderWidth: 1,
                borderRadius: 3
            }]
        },
        options: {
            ...chartDefaults(),
            indexAxis: 'y',
            layout: { padding: { right: 38 } },
            plugins: { ...chartDefaults().plugins, legend: { display: false },
                datalabels: { ...dlBar(), anchor: 'end', align: 'right', color: '#9fa8c0' } },
            scales: {
                x: { ...scaleDefaults(), beginAtZero: true },
                y: { ...scaleDefaults(), ticks: { ...scaleDefaults().ticks, font: { size: 10 } } }
            }
        }
    });

    const pieOpts = { ...chartDefaults(), plugins: { ...chartDefaults().plugins, datalabels: dlPie() } };
    const doughnutOpts = (cut) => ({ ...chartDefaults(), cutout: cut, plugins: { ...chartDefaults().plugins, datalabels: dlPie() } });

    // 5. Anuladas (doughnut)
    DashboardState.charts.anuladas = new Chart(
        document.getElementById('chart-anuladas').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Activas', 'Anuladas'],
            datasets: [{
                data: [0, 0],
                backgroundColor: [COLORS.green + 'cc', COLORS.red + 'cc'],
                borderColor: [COLORS.green, COLORS.red],
                borderWidth: 2
            }]
        },
        options: doughnutOpts('60%')
    });

    // 6. Crédito vs Contado (pie)
    DashboardState.charts.credito = new Chart(
        document.getElementById('chart-credito').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Con Crédito', 'Contado'],
            datasets: [{
                data: [0, 0],
                backgroundColor: [COLORS.blue + 'cc', COLORS.cyan + 'cc'],
                borderColor: [COLORS.blue, COLORS.cyan],
                borderWidth: 2
            }]
        },
        options: pieOpts
    });

    // 7. Toma Usado (pie)
    DashboardState.charts.toma = new Chart(
        document.getElementById('chart-toma').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Con Toma Usado', 'Sin Toma'],
            datasets: [{
                data: [0, 0],
                backgroundColor: [COLORS.orange + 'cc', COLORS.yellow + 'cc'],
                borderColor: [COLORS.orange, COLORS.yellow],
                borderWidth: 2
            }]
        },
        options: pieOpts
    });

    // 8. Compra: Nuevo vs Usado (doughnut)
    DashboardState.charts.compra = new Chart(
        document.getElementById('chart-compra').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Nuevo', 'Usado'],
            datasets: [{
                data: [0, 0],
                backgroundColor: [COLORS.blue + 'cc', COLORS.purple + 'cc'],
                borderColor: [COLORS.blue, COLORS.purple],
                borderWidth: 2
            }]
        },
        options: doughnutOpts('60%')
    });
}

// ══════════════════════════════════════════════════════════
// INICIALIZAR DATATABLE (server-side)
// ══════════════════════════════════════════════════════════
function initDataTable() {
    DashboardState.dtTable = $('#main-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  'api.php?action=table',
            type: 'POST',
            data: function(d) {
                const f = DashboardState.filters;
                d.anio        = f.anio;
                d.mes         = f.mes;
                d.fecha_desde = f.fecha_desde;
                d.fecha_hasta = f.fecha_hasta;
                d.idsucursal  = f.idsucursal;
                d.idusuario   = f.idusuario;
                d.idgrupo     = f.idgrupo;
                d.idmodelo    = f.idmodelo;
                d.anulada     = f.anulada;
                d.credito     = f.credito;
                d.toma_usado  = f.toma_usado;
                return d;
            }
        },
        columns: [
            { data: 'idreserva', className: 'text-center' },
            { data: 'fecres', className: 'text-nowrap' },
            { data: 'sucursal' },
            { data: 'vendedor' },
            {
                data: null,
                render: function(data, type, row) {
                    var compra = (row.compra || '').toLowerCase().trim();
                    var detalle = (row.detalleu || '').trim();
                    if (compra === 'usado' && detalle) {
                        return detalle;
                    }
                    var g = row.grupo && row.grupo !== '—' ? row.grupo : '';
                    var m = row.modelo && row.modelo !== '—' ? row.modelo : '';
                    if (g && m) return g + ' · ' + m;
                    return g || m || '—';
                }
            },
            {
                data: 'compra',
                className: 'text-center',
                render: function(val) {
                    if (!val) return '';
                    var v = val.toLowerCase().trim();
                    return v === 'usado'
                        ? '<span class="badge-anulada">Usado</span>'
                        : '<span class="badge-activa">Nuevo</span>';
                }
            },
            {
                data: 'anulada',
                className: 'text-center',
                render: function(val) {
                    return val == 1
                        ? '<span class="badge-anulada">Sí</span>'
                        : '<span class="badge-activa">No</span>';
                }
            },
            {
                data: 'credito',
                className: 'text-center',
                render: function(val) {
                    return val == 1
                        ? '<span class="badge-si">Sí</span>'
                        : '<span class="badge-no">No</span>';
                }
            },
            {
                data: 'toma_usado',
                className: 'text-center',
                render: function(val) {
                    return val == 1
                        ? '<span class="badge-si">Sí</span>'
                        : '<span class="badge-no">No</span>';
                }
            },
            {
                data: 'idreserva',
                orderable: false,
                className: 'text-center',
                render: function(id) {
                    return '<a class="btn-ver" href="/ventas/web/reserva.php?IDrecord=' + id + '" target="_blank">'
                         + '<i class="fa fa-eye"></i></a>';
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            processing:   "Procesando...",
            search:       "Buscar:",
            lengthMenu:   "Mostrar _MENU_ registros",
            info:         "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty:    "Sin registros",
            infoFiltered: "(filtrado de _MAX_ total)",
            paginate: { first: "«", last: "»", next: "›", previous: "‹" },
            zeroRecords:  "No se encontraron resultados",
            emptyTable:   "Sin datos disponibles"
        },
        dom: '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>'
    });
}

// ══════════════════════════════════════════════════════════
// EVENTOS DE FILTROS
// ══════════════════════════════════════════════════════════
function bindFilterEvents() {
    document.getElementById('btn-apply').addEventListener('click', function() {
        readFiltersFromForm();
        applyFilters();
    });

    document.getElementById('btn-clear').addEventListener('click', function() {
        clearFilters();
        applyFilters();
    });

    // Versiones dependientes del modelo seleccionado
    document.getElementById('f-grupo').addEventListener('change', function() {
        loadVersionesByGrupo(this.value);
        document.getElementById('f-modelo').value = '0';
    });

    // Enter en el formulario aplica
    document.getElementById('filter-form').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            readFiltersFromForm();
            applyFilters();
        }
    });

    // Toggle comparación
    document.getElementById('toggle-comparison').addEventListener('change', function() {
        DashboardState.comparisonMode = this.checked;
        const chart = DashboardState.charts.anioComp;
        if (!chart) return;
        chart.data.datasets[1].hidden = !DashboardState.comparisonMode;
        chart.data.datasets[2].hidden = !DashboardState.comparisonMode;
        chart.update();
        // Mostrar/ocultar tabla comparativa
        const wrap = document.getElementById('comp-table-wrap');
        if (wrap) {
            if (DashboardState.comparisonMode) wrap.classList.remove('d-none');
            else wrap.classList.add('d-none');
        }
        const grupoWrap = document.getElementById('comp-grupo-wrap');
        if (DashboardState.comparisonMode) {
            fetch('api.php?action=chart_anio_comp&' + buildParams())
                .then(r => r.json())
                .then(data => updateChartAnioComp(data))
                .catch(console.error);
            fetch('api.php?action=comp_grupo&' + buildParams())
                .then(r => r.json())
                .then(data => updateCompGrupoTable(data))
                .catch(console.error);
        } else {
            if (grupoWrap) grupoWrap.classList.add('d-none');
        }
    });
}

function readFiltersFromForm() {
    DashboardState.filters.anio        = parseInt(document.getElementById('f-anio').value)     || 0;
    DashboardState.filters.mes         = parseInt(document.getElementById('f-mes').value)      || 0;
    DashboardState.filters.fecha_desde = document.getElementById('f-fecha-desde').value        || '';
    DashboardState.filters.fecha_hasta = document.getElementById('f-fecha-hasta').value        || '';
    DashboardState.filters.idsucursal  = parseInt(document.getElementById('f-sucursal').value) || 0;
    DashboardState.filters.idusuario   = parseInt(document.getElementById('f-vendedor').value) || 0;
    DashboardState.filters.idgrupo     = parseInt(document.getElementById('f-grupo').value)    || 0;
    DashboardState.filters.idmodelo    = parseInt(document.getElementById('f-modelo').value)   || 0;
    DashboardState.filters.anulada     = parseInt(document.getElementById('f-anulada').value);
    DashboardState.filters.credito     = parseInt(document.getElementById('f-credito').value);
    DashboardState.filters.toma_usado  = parseInt(document.getElementById('f-toma').value);
    DashboardState.filters.compra      = document.getElementById('f-compra').value || '';
}

function clearFilters() {
    document.getElementById('f-anio').value = new Date().getFullYear();
    document.getElementById('f-mes').value = '0';
    document.getElementById('f-fecha-desde').value = '';
    document.getElementById('f-fecha-hasta').value = '';
    document.getElementById('f-sucursal').value = '0';
    document.getElementById('f-vendedor').value = '0';
    document.getElementById('f-grupo').value = '0';
    document.getElementById('f-modelo').value = '0';
    document.getElementById('f-anulada').value = '-1';
    document.getElementById('f-credito').value = '-1';
    document.getElementById('f-toma').value = '-1';
    document.getElementById('f-compra').value = '';
    document.getElementById('toggle-comparison').checked = false;
    DashboardState.comparisonMode = false;
    loadVersionesByGrupo(0);
    const wrap = document.getElementById('comp-table-wrap');
    if (wrap) wrap.classList.add('d-none');
    const grupoWrap = document.getElementById('comp-grupo-wrap');
    if (grupoWrap) grupoWrap.classList.add('d-none');

    DashboardState.filters = {
        anio: new Date().getFullYear(),
        mes: 0, fecha_desde: '', fecha_hasta: '',
        idsucursal: 0, idusuario: 0, idgrupo: 0, idmodelo: 0,
        anulada: -1, credito: -1, toma_usado: -1, compra: ''
    };
}

// ══════════════════════════════════════════════════════════
// APLICAR FILTROS — actualiza todo en paralelo
// ══════════════════════════════════════════════════════════
async function applyFilters() {
    showLoading();

    const params = buildParams();
    const actions = [
        'kpis', 'chart_mes', 'chart_anio_comp', 'chart_sucursal',
        'chart_vendedor', 'chart_credito', 'chart_toma', 'chart_anuladas', 'chart_compra'
    ];

    try {
        const results = await Promise.all(
            actions.map(a => fetch('api.php?action=' + a + '&' + params).then(r => r.json()))
        );

        updateKPIs(results[0]);
        updateChartMes(results[1]);
        updateChartAnioComp(results[2]);
        updateChartSucursal(results[3]);
        updateChartVendedor(results[4]);
        updateChartCredito(results[5]);
        updateChartToma(results[6]);
        updateChartAnuladas(results[7]);
        updateChartCompra(results[8]);

        // Si el modo comparación está activo, actualizar también la tabla de grupos
        if (DashboardState.comparisonMode) {
            fetch('api.php?action=comp_grupo&' + params)
                .then(r => r.json())
                .then(data => updateCompGrupoTable(data))
                .catch(console.error);
        }

        // Comparativo mes a mes por modelo (siempre activo)
        fetch('api.php?action=comp_modelo_mes&' + params)
            .then(r => r.json())
            .then(data => renderCompModeloMes(data))
            .catch(console.error);

    } catch(e) {
        console.error('Error al cargar datos:', e);
    }

    // Reload DataTable con nuevos filtros
    if (DashboardState.dtTable) {
        DashboardState.dtTable.ajax.reload(null, false);
    }

    hideLoading();
}

// ══════════════════════════════════════════════════════════
// UPDATE FUNCTIONS
// ══════════════════════════════════════════════════════════
function updateKPIs(data) {
    if (!data) return;
    setText('kpi-total',       formatNum(data.total));
    setText('kpi-nuevas',      formatNum(data.nuevas));
    setText('kpi-usadas',      formatNum(data.usadas));
    setText('kpi-anuladas',    formatNum(data.anuladas));
    setText('kpi-pct-anuladas', data.pct_anuladas + '%');
    setText('kpi-credito',     formatNum(data.con_credito));
    setText('kpi-pct-credito', data.pct_credito + '%');
    setText('kpi-toma',        formatNum(data.con_toma));
    setText('kpi-pct-toma',    data.pct_toma + '%');
}

function updateChartMes(data) {
    if (!data || !DashboardState.charts.mes) return;
    const chart = DashboardState.charts.mes;
    chart.data.datasets[0].data = data.data || Array(12).fill(0);
    if (data.anio) chart.data.datasets[0].label = 'Reservas ' + data.anio;
    chart.update('active');
}

function updateChartAnioComp(data) {
    if (!data || !DashboardState.charts.anioComp) return;
    const chart = DashboardState.charts.anioComp;
    if (data.anio_actual) {
        chart.data.datasets[0].data  = data.anio_actual.data || Array(12).fill(0);
        chart.data.datasets[0].label = data.anio_actual.label || '';
    }
    if (data.anio_anterior) {
        chart.data.datasets[1].data   = data.anio_anterior.data || Array(12).fill(0);
        chart.data.datasets[1].label  = data.anio_anterior.label || '';
        chart.data.datasets[1].hidden = !DashboardState.comparisonMode;
    }
    if (data.anio_anterior2) {
        chart.data.datasets[2].data   = data.anio_anterior2.data || Array(12).fill(0);
        chart.data.datasets[2].label  = data.anio_anterior2.label || '';
        chart.data.datasets[2].hidden = !DashboardState.comparisonMode;
    }
    chart.update('active');
    updateComparisonTable(data);
}

function updateComparisonTable(data) {
    const thead = document.getElementById('comp-table-head');
    const tbody = document.getElementById('comp-table-body');
    if (!thead || !tbody || !data) return;

    const a0 = data.anio_actual    || { label: '', data: Array(12).fill(0), total: 0 };
    const a1 = data.anio_anterior  || { label: '', data: Array(12).fill(0), total: 0 };
    const a2 = data.anio_anterior2 || { label: '', data: Array(12).fill(0), total: 0 };

    const pct = (n, base) => base > 0 ? ((n - base) / base * 100).toFixed(1) : '—';
    const pctHtml = (n, base) => {
        if (base <= 0) return '<td style="color:var(--text-muted);text-align:center">—</td>';
        const v = ((n - base) / base * 100).toFixed(1);
        const color = v >= 0 ? 'var(--accent-green)' : 'var(--accent-red)';
        const sign  = v >= 0 ? '+' : '';
        return '<td style="color:' + color + ';text-align:center;font-weight:600">' + sign + v + '%</td>';
    };

    const th = (txt, extra) => '<th style="background:var(--bg-secondary);color:var(--text-secondary);padding:5px 10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--border-color);text-align:center;' + (extra||'') + '">' + txt + '</th>';
    const td = (txt, extra) => '<td style="padding:5px 10px;border-bottom:1px solid var(--border-color);text-align:center;' + (extra||'') + '">' + txt + '</td>';

    // Header
    thead.innerHTML = '<tr>'
        + th('Mes', 'text-align:left')
        + th(a0.label, 'color:var(--accent-blue)')
        + th(a1.label, 'color:var(--accent-orange)')
        + th('Δ vs ' + a1.label, '')
        + th(a2.label, 'color:var(--accent-green)')
        + th('Δ vs ' + a2.label, '')
        + '</tr>';

    // Filas mensuales
    let rows = '';
    for (let i = 0; i < 12; i++) {
        const v0 = intval(a0.data[i]), v1 = intval(a1.data[i]), v2 = intval(a2.data[i]);
        rows += '<tr>'
            + '<td style="padding:5px 10px;border-bottom:1px solid var(--border-color);color:var(--text-secondary);font-size:11px">' + MONTH_FULL[i] + '</td>'
            + td(v0 > 0 ? v0.toLocaleString('es-AR') : '<span style="color:var(--text-muted)">0</span>', 'font-weight:600;color:var(--accent-blue)')
            + td(v1 > 0 ? v1.toLocaleString('es-AR') : '<span style="color:var(--text-muted)">0</span>', 'color:var(--accent-orange)')
            + pctHtml(v0, v1)
            + td(v2 > 0 ? v2.toLocaleString('es-AR') : '<span style="color:var(--text-muted)">0</span>', 'color:var(--accent-green)')
            + pctHtml(v0, v2)
            + '</tr>';
    }

    // Fila total
    const t0 = intval(a0.total), t1 = intval(a1.total), t2 = intval(a2.total);
    rows += '<tr style="background:var(--bg-secondary)">'
        + '<td style="padding:6px 10px;font-weight:800;color:var(--text-primary);font-size:12px">TOTAL</td>'
        + td(t0.toLocaleString('es-AR'), 'font-weight:800;color:var(--accent-blue);font-size:13px')
        + td(t1.toLocaleString('es-AR'), 'font-weight:700;color:var(--accent-orange)')
        + pctHtml(t0, t1)
        + td(t2.toLocaleString('es-AR'), 'font-weight:700;color:var(--accent-green)')
        + pctHtml(t0, t2)
        + '</tr>';

    tbody.innerHTML = rows;
}

function updateCompGrupoTable(data) {
    const wrap  = document.getElementById('comp-grupo-wrap');
    const thead = document.getElementById('comp-grupo-head');
    const tbody = document.getElementById('comp-grupo-body');
    if (!thead || !tbody || !data || !data.rows) return;

    const anios = data.anios || ['', '', ''];
    const A0 = anios[0], A1 = anios[1], A2 = anios[2];

    const pctHtml = (n, base) => {
        if (base <= 0) return '<td style="color:var(--text-muted);text-align:center;padding:5px 10px;border-bottom:1px solid var(--border-color)">—</td>';
        const v = ((n - base) / base * 100).toFixed(1);
        const color = v >= 0 ? 'var(--accent-green)' : 'var(--accent-red)';
        const sign  = v >= 0 ? '+' : '';
        return '<td style="color:' + color + ';text-align:center;font-weight:600;padding:5px 10px;border-bottom:1px solid var(--border-color)">' + sign + v + '%</td>';
    };
    const th = (txt, extra) => '<th style="background:var(--bg-secondary);color:var(--text-secondary);padding:5px 10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--border-color);text-align:center;' + (extra||'') + '">' + txt + '</th>';
    const td = (txt, extra) => '<td style="padding:5px 10px;border-bottom:1px solid var(--border-color);text-align:center;' + (extra||'') + '">' + txt + '</td>';

    thead.innerHTML = '<tr>'
        + th('Grupo', 'text-align:left')
        + th(A0, 'color:var(--accent-blue)')
        + th(A1, 'color:var(--accent-orange)')
        + th('Δ vs ' + A1)
        + th(A2, 'color:var(--accent-green)')
        + th('Δ vs ' + A2)
        + th('Total ' + A0, 'color:var(--accent-blue)')
        + '</tr>';

    let rows = '';
    let tot0 = 0, tot1 = 0, tot2 = 0;

    data.rows.forEach(function(r) {
        const v0 = parseInt(r.a0) || 0;
        const v1 = parseInt(r.a1) || 0;
        const v2 = parseInt(r.a2) || 0;
        tot0 += v0; tot1 += v1; tot2 += v2;
        rows += '<tr>'
            + '<td style="padding:5px 10px;border-bottom:1px solid var(--border-color);color:var(--text-primary);font-weight:500">' + r.grupo + '</td>'
            + td(v0 > 0 ? v0.toLocaleString('es-AR') : '<span style="color:var(--text-muted)">0</span>', 'font-weight:600;color:var(--accent-blue)')
            + td(v1 > 0 ? v1.toLocaleString('es-AR') : '<span style="color:var(--text-muted)">0</span>', 'color:var(--accent-orange)')
            + pctHtml(v0, v1)
            + td(v2 > 0 ? v2.toLocaleString('es-AR') : '<span style="color:var(--text-muted)">0</span>', 'color:var(--accent-green)')
            + pctHtml(v0, v2)
            + td(v0.toLocaleString('es-AR'), 'color:var(--text-secondary)')
            + '</tr>';
    });

    // Fila total
    rows += '<tr style="background:var(--bg-secondary)">'
        + '<td style="padding:6px 10px;font-weight:800;color:var(--text-primary);font-size:12px">TOTAL</td>'
        + td(tot0.toLocaleString('es-AR'), 'font-weight:800;color:var(--accent-blue);font-size:13px')
        + td(tot1.toLocaleString('es-AR'), 'font-weight:700;color:var(--accent-orange)')
        + pctHtml(tot0, tot1)
        + td(tot2.toLocaleString('es-AR'), 'font-weight:700;color:var(--accent-green)')
        + pctHtml(tot0, tot2)
        + td(tot0.toLocaleString('es-AR'), 'font-weight:800;color:var(--text-secondary)')
        + '</tr>';

    tbody.innerHTML = rows;
    if (wrap) wrap.classList.remove('d-none');
}

function updateChartSucursal(data) {
    updateBarChart(DashboardState.charts.sucursal, data, COLORS.green);
}

function updateChartVendedor(data) {
    updateBarChart(DashboardState.charts.vendedor, data, COLORS.purple);
}

function updateBarChart(chart, data, color) {
    if (!chart || !data) return;
    const values = data.data || [];
    const total = values.reduce((s, v) => s + (parseInt(v) || 0), 0);
    chart.data.labels = data.labels || [];
    chart.data.datasets[0].data = values;
    chart.data.datasets[0].backgroundColor = (data.labels || []).map(() => color + 'bb');
    chart.data.datasets[0].borderColor     = (data.labels || []).map(() => color);
    chart.options.plugins.datalabels.formatter = function(value) {
        if (!value) return '';
        var pct = total > 0 ? (value / total * 100).toFixed(1) : '0.0';
        return value.toLocaleString('es-AR') + ' (' + pct + '%)';
    };
    chart.update('active');
}

function updateChartCredito(data) {
    updatePieChart(DashboardState.charts.credito, data);
}

function updateChartToma(data) {
    updatePieChart(DashboardState.charts.toma, data);
}

function updateChartAnuladas(data) {
    updatePieChart(DashboardState.charts.anuladas, data);
}

function updateChartCompra(data) {
    updatePieChart(DashboardState.charts.compra, data);
}

function updatePieChart(chart, data) {
    if (!chart || !data) return;
    chart.data.labels           = data.labels || [];
    chart.data.datasets[0].data = data.data   || [];
    chart.update('active');
}

// ══════════════════════════════════════════════════════════
// EXPORTACIONES
// ══════════════════════════════════════════════════════════
function exportExcel() {
    window.open('export_excel.php?' + buildParams(), '_blank');
}

function exportPDF() {
    window.open('export_pdf.php?' + buildParams(), '_blank');
}

function openReport() {
    const form = document.createElement('form');
    form.method  = 'POST';
    form.action  = 'reporte.php';
    form.target  = '_blank';

    // Filtros como hidden inputs
    const f = DashboardState.filters;
    Object.entries(f).forEach(function([key, val]) {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = key;
        input.value = val;
        form.appendChild(input);
    });

    // Imágenes de los charts con fondo BLANCO (para el reporte imprimible)
    const chartNames = {
        mes:       'chart_img_mes',
        anioComp:  'chart_img_anio_comp',
        sucursal:  'chart_img_sucursal',
        vendedor:  'chart_img_vendedor',
        anuladas:  'chart_img_anuladas',
        credito:   'chart_img_credito',
        toma:      'chart_img_toma',
        compra:    'chart_img_compra'
    };
    Object.entries(chartNames).forEach(function([key, name]) {
        const chart = DashboardState.charts[key];
        if (!chart) return;
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = name;
        input.value = chartToWhiteBg(chart);
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// ══════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════
// Captura un chart con fondo blanco (para reporte imprimible)
function chartToWhiteBg(chart) {
    const src = chart.canvas;
    const tmp = document.createElement('canvas');
    tmp.width  = src.width;
    tmp.height = src.height;
    const ctx = tmp.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, tmp.width, tmp.height);
    ctx.drawImage(src, 0, 0);
    return tmp.toDataURL('image/jpeg', 0.88);
}

function buildParams() {
    const f = DashboardState.filters;
    const parts = [];
    Object.entries(f).forEach(function([k, v]) {
        parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
    });
    return parts.join('&');
}

function showLoading() {
    const ol = document.getElementById('loading-overlay');
    if (ol) ol.classList.add('active');
}

function hideLoading() {
    const ol = document.getElementById('loading-overlay');
    if (ol) ol.classList.remove('active');
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function formatNum(n) {
    n = parseInt(n) || 0;
    return n.toLocaleString('es-AR');
}

// ══════════════════════════════════════════════════════════
// COMPARATIVO MES A MES POR MODELO / VERSIÓN  (acordeón)
// ══════════════════════════════════════════════════════════
function renderCompModeloMes(data) {
    const thead = document.getElementById('modelo-mes-thead');
    const tbody = document.getElementById('modelo-mes-tbody');
    const empty = document.getElementById('modelo-mes-empty');
    if (!thead || !tbody) return;

    if (!data || !data.rows || data.rows.length === 0) {
        thead.innerHTML = '';
        tbody.innerHTML = '';
        if (empty) empty.style.display = 'block';
        return;
    }
    if (empty) empty.style.display = 'none';

    const MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

    // ── Pivot manteniendo el orden que llega del servidor (ya ordenado por posicion)
    const grupos = {};      // { grupoNombre: { modelos: { key: {...} }, modeloOrder: [], mesTotals: {}, total } }
    const grupoOrder = [];  // mantener orden de inserción

    data.rows.forEach(function(r) {
        const gKey = r.grupo || '—';
        const mKey = gKey + '||' + (r.modelo || '—');

        if (!grupos[gKey]) {
            grupos[gKey] = { nombre: r.grupo, modelos: {}, modeloOrder: [], mesTotals: {}, total: 0 };
            grupoOrder.push(gKey);
        }
        const g = grupos[gKey];

        if (!g.modelos[mKey]) {
            g.modelos[mKey] = { nombre: r.modelo, meses: {}, total: 0 };
            g.modeloOrder.push(mKey);
        }

        const mes  = parseInt(r.mes);
        const cant = parseInt(r.cantidad) || 0;

        g.modelos[mKey].meses[mes]  = cant;
        g.modelos[mKey].total      += cant;
        g.mesTotals[mes]            = (g.mesTotals[mes] || 0) + cant;
        g.total                    += cant;
    });

    // ── Meses activos (solo los que tienen datos)
    const mesesSet = new Set();
    grupoOrder.forEach(gk => Object.keys(grupos[gk].mesTotals).forEach(m => mesesSet.add(parseInt(m))));
    const mesesActivos = Array.from(mesesSet).sort((a, b) => a - b);

    // ── Helpers de estilo
    const thBase = 'background:var(--bg-secondary);color:var(--text-secondary);padding:5px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--border-color);white-space:nowrap;';
    const tdBase = 'padding:4px 8px;border-bottom:1px solid var(--border-color);text-align:center;';

    function pctBadge(actual, anterior) {
        if (anterior <= 0) return '';
        const v = ((actual - anterior) / anterior * 100).toFixed(1);
        const color = parseFloat(v) >= 0 ? '#63c795' : '#e05c5c';
        const sign  = parseFloat(v) >= 0 ? '+' : '';
        return '<br><span style="font-size:9px;font-weight:700;color:' + color + '">' + sign + v + '%</span>';
    }

    function cellVal(val, bold) {
        if (val <= 0) return '<span style="color:var(--text-muted)">—</span>';
        return bold ? '<b>' + val + '</b>' : String(val);
    }

    // ── THEAD
    let headRow = '<tr>'
        + '<th style="' + thBase + 'text-align:left;min-width:200px;padding-left:10px">Grupo / Versión</th>';
    mesesActivos.forEach(function(m) {
        headRow += '<th style="' + thBase + 'text-align:center;min-width:68px">' + MESES[m - 1] + '</th>';
    });
    headRow += '<th style="' + thBase + 'text-align:center;color:var(--accent-blue);min-width:60px">Total</th></tr>';
    thead.innerHTML = headRow;

    // ── TBODY con acordeón
    const totMes = {};
    mesesActivos.forEach(m => { totMes[m] = 0; });
    let totGlobal = 0;
    let rows = '';

    grupoOrder.forEach(function(gk, gi) {
        const g = grupos[gk];

        // ─ Fila GRUPO (cabecera acordeón)
        rows += '<tr class="mm-grupo-row" data-gi="' + gi + '" data-nombre="' + escHtml(gk) + '" '
              + 'style="cursor:pointer;background:var(--bg-secondary);user-select:none;" '
              + 'onclick="modeloMesToggleGrupo(' + gi + ')">';
        rows += '<td style="' + tdBase + 'text-align:left;font-weight:700;color:var(--text-primary);font-size:12px;padding-left:10px;">'
              + '<i id="mm-chev-' + gi + '" class="fa fa-chevron-right" style="font-size:9px;margin-right:6px;transition:transform .2s;color:var(--accent-cyan)"></i>'
              + escHtml(g.nombre) + '</td>';

        mesesActivos.forEach(function(m, idx) {
            const val  = g.mesTotals[m] || 0;
            const prev = idx > 0 ? (g.mesTotals[mesesActivos[idx - 1]] || 0) : null;
            totMes[m] += val;
            totGlobal += val;
            const badge = prev !== null ? pctBadge(val, prev) : '';
            rows += '<td style="' + tdBase + 'font-weight:700;color:var(--accent-orange)">'
                  + cellVal(val, true) + badge + '</td>';
        });
        rows += '<td style="' + tdBase + 'font-weight:800;color:var(--accent-orange)">'
              + g.total.toLocaleString('es-AR') + '</td>';
        rows += '</tr>';

        // ─ Filas VERSIÓN (ocultas por defecto)
        g.modeloOrder.forEach(function(mk) {
            const mod = g.modelos[mk];
            rows += '<tr class="mm-version-row mm-gi-' + gi + '" '
                  + 'data-gi="' + gi + '" data-nombre="' + escHtml(gk + ' ' + mod.nombre) + '" '
                  + 'style="display:none;">';
            rows += '<td style="' + tdBase + 'text-align:left;color:var(--text-secondary);padding-left:28px;">'
                  + '<i class="fa fa-minus" style="font-size:8px;margin-right:6px;color:var(--border-color)"></i>'
                  + escHtml(mod.nombre) + '</td>';

            mesesActivos.forEach(function(m, idx) {
                const val  = mod.meses[m] || 0;
                const prev = idx > 0 ? (mod.meses[mesesActivos[idx - 1]] || 0) : null;
                const badge = prev !== null ? pctBadge(val, prev) : '';
                rows += '<td style="' + tdBase + '">' + cellVal(val, false) + badge + '</td>';
            });
            rows += '<td style="' + tdBase + 'color:var(--text-secondary)">'
                  + mod.total.toLocaleString('es-AR') + '</td>';
            rows += '</tr>';
        });
    });

    // ─ Fila TOTAL GENERAL
    rows += '<tr style="background:var(--bg-secondary);border-top:2px solid var(--border-color)">';
    rows += '<td style="' + tdBase + 'text-align:left;font-weight:800;color:var(--text-primary);font-size:12px;padding-left:10px">TOTAL GENERAL</td>';
    let acum = 0;
    mesesActivos.forEach(function(m, idx) {
        const val  = totMes[m];
        const prev = idx > 0 ? totMes[mesesActivos[idx - 1]] : null;
        acum += val;
        const badge = prev !== null ? pctBadge(val, prev) : '';
        rows += '<td style="' + tdBase + 'font-weight:700;color:var(--accent-blue)">'
              + val.toLocaleString('es-AR') + badge + '</td>';
    });
    rows += '<td style="' + tdBase + 'font-weight:800;color:var(--accent-blue);font-size:13px">'
          + acum.toLocaleString('es-AR') + '</td>';
    rows += '</tr>';

    tbody.innerHTML = rows;

    // ── Búsqueda en tiempo real
    bindModeloMesSearch();
}

// Toggle individual de un grupo
function modeloMesToggleGrupo(gi) {
    const rows  = document.querySelectorAll('.mm-gi-' + gi);
    const chev  = document.getElementById('mm-chev-' + gi);
    const isOpen = chev && chev.style.transform === 'rotate(90deg)';
    rows.forEach(function(tr) { tr.style.display = isOpen ? 'none' : ''; });
    if (chev) chev.style.transform = isOpen ? '' : 'rotate(90deg)';
}

// Expandir o colapsar todos los grupos
function modeloMesExpandAll(open) {
    const tbody = document.getElementById('modelo-mes-tbody');
    if (!tbody) return;
    tbody.querySelectorAll('.mm-version-row').forEach(function(tr) {
        tr.style.display = open ? '' : 'none';
    });
    tbody.querySelectorAll('[id^="mm-chev-"]').forEach(function(chev) {
        chev.style.transform = open ? 'rotate(90deg)' : '';
    });
}

function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function bindModeloMesSearch() {
    const input = document.getElementById('modelo-mes-search');
    if (!input) return;
    input.oninput = function() {
        const q = this.value.trim().toLowerCase();
        const tbody = document.getElementById('modelo-mes-tbody');
        if (!tbody) return;

        if (q === '') {
            // Restaurar: solo mostrar filas de grupo, ocultar versiones
            tbody.querySelectorAll('.mm-grupo-row').forEach(tr => { tr.style.display = ''; });
            tbody.querySelectorAll('.mm-version-row').forEach(function(tr) {
                const gi   = tr.dataset.gi;
                const chev = document.getElementById('mm-chev-' + gi);
                const open = chev && chev.style.transform === 'rotate(90deg)';
                tr.style.display = open ? '' : 'none';
            });
            return;
        }

        // Con búsqueda: mostrar solo filas cuyo nombre coincide
        const matchGi = new Set();
        tbody.querySelectorAll('.mm-version-row').forEach(function(tr) {
            const match = tr.dataset.nombre && tr.dataset.nombre.toLowerCase().includes(q);
            tr.style.display = match ? '' : 'none';
            if (match) matchGi.add(tr.dataset.gi);
        });
        // Mostrar u ocultar filas de grupo según si tienen versiones coincidentes
        tbody.querySelectorAll('.mm-grupo-row').forEach(function(tr) {
            const grupoNombre = tr.dataset.nombre && tr.dataset.nombre.toLowerCase().includes(q);
            const tieneHijos  = matchGi.has(tr.dataset.gi);
            tr.style.display  = (grupoNombre || tieneHijos) ? '' : 'none';
            if (tieneHijos) {
                // Asegurarse de que estén visibles las versiones coincidentes
                const chev = document.getElementById('mm-chev-' + tr.dataset.gi);
                if (chev) chev.style.transform = 'rotate(90deg)';
            }
        });
    };
}
