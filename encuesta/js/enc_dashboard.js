/**
 * enc_dashboard.js — Dashboard Encuestas de Satisfacción
 */
'use strict';

const API = 'api_dashboard.php';

const State = {
    filters: {
        anio: new Date().getFullYear(),
        mes: 0,
        fecha_desde: '', fecha_hasta: '',
        idsucursal: 0, id_asesor: 0,
        idgrupo: 0, idmodelo: 0,
        id_area: 0
    },
    charts: {}
};

const MONTH_LABELS = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

// ── Colores ──────────────────────────────────────────────────
function scoreColor(val) {
    if (val === null || val === undefined) return '#6b7394';
    if (val >= 8) return '#63c795';
    if (val >= 6) return '#f1a84e';
    return '#e05c5c';
}

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    restoreTheme();
    loadFilters();
    initCharts();
    initDataTable();
    bindEvents();
    applyFilters();
});

// ── Tema ─────────────────────────────────────────────────────
function restoreTheme() {
    const saved = localStorage.getItem('enc-dash-theme') || 'dark';
    document.body.dataset.theme = saved;
    updateThemeIcon(saved);
}
function updateThemeIcon(theme) {
    const icon = document.getElementById('theme-icon');
    if (icon) icon.className = theme === 'dark' ? 'fa fa-sun' : 'fa fa-moon';
}
document.getElementById('theme-toggle').addEventListener('click', function () {
    const next = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
    document.body.dataset.theme = next;
    localStorage.setItem('enc-dash-theme', next);
    updateThemeIcon(next);
    refreshChartsTheme(next);
});
function refreshChartsTheme(theme) {
    const textColor = theme === 'dark' ? '#9fa8c0' : '#4a5568';
    const gridColor = theme === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.08)';
    Object.values(State.charts).forEach(ch => {
        if (!ch) return;
        if (ch.options.plugins?.legend) ch.options.plugins.legend.labels.color = textColor;
        if (ch.options.scales) {
            Object.values(ch.options.scales).forEach(ax => {
                if (ax.ticks) ax.ticks.color = textColor;
                if (ax.grid)  ax.grid.color  = gridColor;
            });
        }
        ch.update('none');
    });
}

// ── Cargar filtros desde API ──────────────────────────────────
function loadFilters() {
    $.get(API, { action: 'filters' }, function (data) {
        populateSelect('f-sucursal', data.sucursales, 'Todas');
        populateSelect('f-asesor',   data.asesores,   'Todos');
        populateSelect('f-grupo',    data.grupos,     'Todos');
        populateSelect('f-modelo',   data.modelos,    'Todas');
        populateSelect('f-area',     data.areas,      'Todas');

        // Años
        const $anio = $('#f-anio');
        $anio.empty();
        (data.anios || []).forEach(a => {
            $anio.append(`<option value="${a}"${a == State.filters.anio ? ' selected' : ''}>${a}</option>`);
        });
        $anio.append('<option value="0">Todos los años</option>');
    });
}
function populateSelect(id, items, allLabel) {
    const $sel = $(`#${id}`);
    $sel.empty().append(`<option value="0">${allLabel}</option>`);
    (items || []).forEach(item => {
        $sel.append(`<option value="${item.id}">${item.label}</option>`);
    });
}

// ── Eventos ───────────────────────────────────────────────────
function bindEvents() {
    $('#btn-apply').on('click', applyFilters);
    $('#btn-clear').on('click', clearFilters);
    $('#f-grupo').on('change', function () {
        // Filtrar modelos según grupo seleccionado (opcional — mantenemos todos por ahora)
    });
}

function readFilters() {
    State.filters.anio        = parseInt($('#f-anio').val())     || 0;
    State.filters.mes         = parseInt($('#f-mes').val())      || 0;
    State.filters.fecha_desde = $('#f-fecha-desde').val()        || '';
    State.filters.fecha_hasta = $('#f-fecha-hasta').val()        || '';
    State.filters.idsucursal  = parseInt($('#f-sucursal').val()) || 0;
    State.filters.id_asesor   = parseInt($('#f-asesor').val())   || 0;
    State.filters.idgrupo     = parseInt($('#f-grupo').val())    || 0;
    State.filters.idmodelo    = parseInt($('#f-modelo').val())   || 0;
    State.filters.id_area     = parseInt($('#f-area').val())     || 0;
}

function clearFilters() {
    $('#f-anio').val(new Date().getFullYear());
    $('#f-mes,#f-sucursal,#f-asesor,#f-grupo,#f-modelo,#f-area').val(0);
    $('#f-fecha-desde,#f-fecha-hasta').val('');
    applyFilters();
}

function applyFilters() {
    readFilters();
    showLoading(true);

    const params = { ...State.filters };

    Promise.all([
        fetchAndRender('kpis',            params, renderKpis),
        fetchAndRender('chart_tendencia', params, renderTendencia),
        fetchAndRender('chart_sucursal',  params, renderSucursal),
        fetchAndRender('chart_asesor',    params, renderAsesor),
        fetchAndRender('chart_areas',     params, renderAreas),
        fetchAndRender('chart_dist',      params, renderDist),
        fetchTable(params),
    ]).finally(() => showLoading(false));
}

function fetchAndRender(action, params, callback) {
    return $.get(API, { action, ...params }).then(callback).catch(err => {
        console.warn('Error en ' + action, err);
    });
}

// ── Loading ───────────────────────────────────────────────────
function showLoading(show) {
    $('#loading-overlay').toggleClass('active', show);
}

// ── KPIs ──────────────────────────────────────────────────────
function renderKpis(data) {
    // Tarjeta promedio general
    const prom = data.promedio !== null ? parseFloat(data.promedio).toFixed(1) : '—';
    $('#kpi-promedio').text(prom);
    $('#kpi-promedio-card').css('border-top-color', scoreColor(parseFloat(prom)));

    $('#kpi-total').text(data.total ?? '—');
    $('#kpi-exc').text((data.pct_exc ?? 0) + '%');
    $('#kpi-exc-sub').text(data.excelentes + ' encuestas ≥ 9');
    $('#kpi-reg').text((data.pct_reg ?? 0) + '%');
    $('#kpi-reg-sub').text(data.regulares + ' encuestas < 7');

    // Áreas dinámicas
    const $row = $('#areas-row').empty();
    if (data.areas && data.areas.length > 0) {
        data.areas.forEach(ar => {
            const val = ar.promedio !== null ? parseFloat(ar.promedio).toFixed(1) : '—';
            const col = ar.color || '#4e9af1';
            const tc  = scoreColor(parseFloat(val));
            $row.append(`
                <div class="area-kpi-card" style="border-top-color:${col}">
                    <div class="area-kpi-label" style="color:${col}">${escHtml(ar.nombre)}</div>
                    <div class="area-kpi-value" style="color:${tc}">${val}</div>
                    <div class="area-kpi-sub">${ar.total} encuestas</div>
                </div>`);
        });
        $('#areas-section').show();
    } else {
        $('#areas-section').hide();
    }
}

// ── Chart: Tendencia mensual ──────────────────────────────────
function initCharts() {
    const theme = document.body.dataset.theme || 'dark';
    const textColor = theme === 'dark' ? '#9fa8c0' : '#4a5568';
    const gridColor = theme === 'dark' ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.08)';

    const defaults = {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { labels: { color: textColor, font: { size: 11 } } }, tooltip: { mode: 'index', intersect: false } },
        scales: {
            x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } },
            y: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } }
        }
    };

    // Tendencia
    State.charts.tendencia = new Chart(document.getElementById('chart-tendencia'), {
        type: 'line',
        data: { labels: [], datasets: [
            { label: 'Promedio', data: [], borderColor: '#63c795', backgroundColor: 'rgba(99,199,149,.12)',
              tension: .4, fill: true, pointRadius: 5, pointHoverRadius: 7, yAxisID: 'y' },
            { label: 'Cantidad', data: [], borderColor: '#4e9af1', backgroundColor: 'rgba(78,154,241,.08)',
              tension: .3, fill: false, pointRadius: 4, borderDash: [4,3], yAxisID: 'y2' }
        ]},
        options: { ...defaults,
            scales: {
                x: { ticks: { color: textColor, font:{size:11} }, grid: { color: gridColor } },
                y:  { min: 0, max: 10, title: { display: true, text: 'Promedio (0-10)', color: textColor, font:{size:11} },
                      ticks: { color: textColor }, grid: { color: gridColor } },
                y2: { position: 'right', title: { display: true, text: 'Cantidad', color: textColor, font:{size:11} },
                      ticks: { color: textColor }, grid: { display: false } }
            }
        }
    });

    // Sucursal
    State.charts.sucursal = new Chart(document.getElementById('chart-sucursal'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Promedio', data: [], backgroundColor: '#4e9af1', borderRadius: 6 }] },
        options: { ...defaults, indexAxis: 'y',
            scales: { x: { min: 0, max: 10, ticks: { color: textColor }, grid: { color: gridColor } },
                      y: { ticks: { color: textColor }, grid: { color: gridColor } } },
            plugins: { ...defaults.plugins, datalabels: { anchor: 'end', align: 'right', color: textColor, font: { size: 11 } } }
        }
    });

    // Asesor
    State.charts.asesor = new Chart(document.getElementById('chart-asesor'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Promedio', data: [], backgroundColor: '#a78bfa', borderRadius: 6 }] },
        options: { ...defaults, indexAxis: 'y',
            scales: { x: { min: 0, max: 10, ticks: { color: textColor }, grid: { color: gridColor } },
                      y: { ticks: { color: textColor }, grid: { color: gridColor } } },
            plugins: { ...defaults.plugins, datalabels: { anchor: 'end', align: 'right', color: textColor, font: { size: 11 } } }
        }
    });

    // Áreas
    State.charts.areas = new Chart(document.getElementById('chart-areas'), {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Promedio por área', data: [], backgroundColor: [], borderRadius: 8 }] },
        options: { ...defaults,
            scales: { x: { ticks: { color: textColor }, grid: { color: gridColor } },
                      y: { min: 0, max: 10, ticks: { color: textColor }, grid: { color: gridColor } } },
            plugins: { ...defaults.plugins, datalabels: { anchor: 'end', align: 'top', color: textColor, font: { size: 12, weight: 'bold' } } }
        }
    });

    // Distribución
    State.charts.dist = new Chart(document.getElementById('chart-dist'), {
        type: 'doughnut',
        data: { labels: [], datasets: [{ data: [], backgroundColor: [], borderWidth: 2, borderColor: 'transparent', hoverOffset: 8 }] },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: {
                legend: { position: 'right', labels: { color: textColor, font: { size: 11 }, padding: 14 } },
                datalabels: { color: '#fff', font: { size: 12, weight: 'bold' },
                    formatter: (val, ctx) => {
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        return total > 0 ? Math.round(val / total * 100) + '%' : '';
                    }
                }
            }
        }
    });
}

function renderTendencia(data) {
    const ch = State.charts.tendencia;
    ch.data.labels         = data.labels   || [];
    ch.data.datasets[0].data = data.promedios || [];
    ch.data.datasets[1].data = data.cantidades || [];
    ch.update();
}

function renderSucursal(data) {
    const ch = State.charts.sucursal;
    ch.data.labels             = data.labels   || [];
    ch.data.datasets[0].data   = data.promedios || [];
    ch.data.datasets[0].backgroundColor = (data.promedios || []).map(v => scoreColor(v) + 'cc');
    ch.update();
}

function renderAsesor(data) {
    const ch = State.charts.asesor;
    ch.data.labels           = data.labels   || [];
    ch.data.datasets[0].data = data.promedios || [];
    ch.data.datasets[0].backgroundColor = (data.promedios || []).map(v => scoreColor(v) + 'cc');
    ch.update();
}

function renderAreas(data) {
    const ch = State.charts.areas;
    ch.data.labels                        = data.labels   || [];
    ch.data.datasets[0].data              = data.promedios || [];
    ch.data.datasets[0].backgroundColor   = (data.colores || []).map(c => c + 'cc');
    ch.data.datasets[0].borderColor       = data.colores || [];
    ch.data.datasets[0].borderWidth       = 2;
    ch.update();
}

function renderDist(data) {
    const ch = State.charts.dist;
    ch.data.labels                      = data.labels    || [];
    ch.data.datasets[0].data            = data.cantidades || [];
    ch.data.datasets[0].backgroundColor = data.colores   || [];
    ch.update();
}

// ── DataTable ─────────────────────────────────────────────────
let dtTable = null;
function initDataTable() {
    dtTable = $('#main-table').DataTable({
        data: [],
        columns: [
            { title: 'Fecha' },
            { title: 'Cliente' },
            { title: 'Grupo' },
            { title: 'Versión' },
            { title: 'Asesor' },
            { title: 'Sucursal' },
            { title: 'Resultado', className: 'text-center' },
            { title: '', width: '90px', orderable: false, className: 'text-center' },
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            url: '', lengthMenu: 'Mostrar _MENU_', info: '_START_–_END_ de _TOTAL_',
            infoEmpty: 'Sin datos', infoFiltered: '(filtrado de _MAX_)',
            search: 'Buscar:', paginate: { previous: '‹', next: '›' },
            zeroRecords: 'Sin resultados para los filtros aplicados',
            emptyTable: 'Aún no hay encuestas completadas'
        },
        columnDefs: [
            { targets: 6, createdCell: function(td) { $(td).css('text-align','center'); } },
            { targets: 7, createdCell: function(td) { $(td).css('text-align','center'); } }
        ]
    });
}

function fetchTable(params) {
    return $.get(API, { action: 'table', ...params }, function (data) {
        if (!dtTable) return;
        dtTable.clear();
        if (data.data && data.data.length > 0) dtTable.rows.add(data.data);
        dtTable.draw();
    });
}

// ── Util ──────────────────────────────────────────────────────
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
