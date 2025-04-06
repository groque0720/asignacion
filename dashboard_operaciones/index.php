<?php
$title = 'Acerca de Nosotros';
ob_start();
include 'config/config_app.php';

$mes_actual = $_GET['mes'] ?? date('m');
$año_actual = $_GET['año'] ?? date('Y');

include 'actions/reservas_descuento.php';
?>






<!-- <div class="flex-1 space-y-4 p-8 pt-6">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <div class="col-span-2 lg:col-span-6">
            <h2 class="text-lg font-medium">Cantidad de Reservas</h2>
        </div>  
        <?php
        // include 'actions/cantidad_reservas.php';
        // foreach ($operaciones as $operacion) {
        //     $title_card = $operacion['sucursal'];
        //     $value_card = $operacion['total_reservas'];
        //     $porcentaje_card = $operacion['porcentaje'] ?? 0;
        //     include('views/components/card_numero.php');
        // }
        ?>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <div class="col-span-2 lg:col-span-6">
            <h2 class="text-lg font-medium">Reservas con Descuento</h2>
        </div>  
        <?php
        // include 'actions/cantidad_reservas_con_descuento.php';
        // foreach ($operaciones as $operacion) {
        //     $title_card = $operacion['sucursal'];
        //     $value_card = $operacion['total_reservas'];
        //     $porcentaje_card = $operacion['porcentaje'] ?? 0;
        //     include('views/components/card_numero.php');
        // }
        ?>
    </div>
</div> -->



























<!-- <canvas id="chartSucursal"></canvas> -->

<!-- <script>
    new Chart(document.getElementById("chartSucursal"), {
        type: 'bar',
        data: {
            labels: ["Sucursal A", "Sucursal B"],
            datasets: [{
                label: 'Descuentos',
                data: [50000, 25000],
                backgroundColor: ['#3b82f6', '#10b981']
            }]
        }
    });
</script> -->


<?php
$content = ob_get_clean();
include __DIR__ . '/views/layouts/main.php';
