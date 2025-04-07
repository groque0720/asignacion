<?php
$title = 'Acerca de Nosotros';
ob_start();
include 'config/config_app.php';


$mes_actual = $_GET['mes'] ?? date('m');
$año_actual = $_GET['año'] ?? date('Y');


$fecha_actual = DateTime::createFromFormat('Y-m', "$año_actual-$mes_actual");

// Mes anterior
$anterior = clone $fecha_actual;
$anterior->modify('-1 month');
$mes_anterior = $anterior->format('m');
$año_anterior = $anterior->format('Y');

// Mes siguiente
$siguiente = clone $fecha_actual;
$siguiente->modify('+1 month');
$mes_siguiente = $siguiente->format('m');
$año_siguiente = $siguiente->format('Y');



include 'actions/reservas_descuento.php';
?>

<div class="flex justify-between items-center p-3 bg-white shadow-md">
    <div class="flex items-center gap-2">
        <img src="/login/imagenes/logo_dyv.png" alt="Logo" class="w-12 h-12 rounded-full">
        <h1 class="text-xl font-bold text-gray-700">Dashboard Operaciones Descuentos</h1>
    </div>
    <div class="flex items-center gap-4">
        <a href="?mes=<?= $mes_anterior ?>&año=<?= $año_anterior ?>" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>    
            Anterior
        </a>
        <!-- <span class="text-gray-600 font-semibold"><?= ucfirst(strftime('%B', $fecha_actual->getTimestamp())) . " $año_actual" ?></span> -->
        <span class="text-gray-600 font-semibold"><?=  nombre_del_mes($mes_actual). " $año_actual" ?></span>
        <a href="?mes=<?= $mes_siguiente ?>&año=<?= $año_siguiente ?>" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
            Siguiente
            <i data-lucide="arrow-right" class="w-5 h-5"></i>    
        </a>
    </div>
</div>


<div class="p-2">
    <span class="text-lg font-bold text-gray-700"><?php echo "Acumulado a Mes ".nombre_del_mes($mes_actual)." Año ".$año_actual; ?></span>
</div>
<div class="grid grid-cols-3 p-3 lg:grid-cols-5 gap-4" >
    <div class="flex flex-col gap-4 bg-white border shadow-md p-2 rounded-md border-gray-300 ">
        <div class="flex flex-col">
            <span class="text-lg font-bold text-gray-700 text-center">Derka y Vargas</span>
            <div>
                <span class="text-xs text-gray-500">Reservas</span>
                <div class="flex justify-center items-center gap-2">
                    <span class="text-2xl text-green-500 font-bold pl-5">
                        <?php echo number_format($dyv['dyv']['año']['cantidad_reservas'], 0, ',', '.') ;?></span>
                    <span class="text-xl text-gray-700 pr-5">
                        <?php // echo number_format(100, 0, ',', '.').'%';?>
                    </span>
                </div>
            </div>
            <div>
                <span class="text-xs text-gray-500">Reservas Con Descuento</span>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-xl text-red-500 font-bold pl-5">
                        <?php echo number_format($dyv['dyv']['año']['cantidad_reservas_con_descuento'], 0, ',', '.') ;?></span>
                    <span class="text-xl text-red-500 pr-5">
                        <?php echo number_format($dyv['dyv']['año']['porcentaje'], 0, ',', '.').'%' ;?></span>
                </div>
            </div>
            <div class="my-2 border border-gray-300"></div>
            <div>
                <span class="text-xs text-gray-500">Monto Operación</span>
                <div class="flex justify-center items-center gap-2">
                    <span class="text-lg text-blue-500 font-bold text-center">
                        <?php echo '$ '.number_format($dyv['dyv']['año']['monto_reservas'], 0, ',', '.') ;?></span>
                    <!-- <span class="text-xl text-blue-500 "> -->
                        <?php // echo number_format(100, 0, ',', '.').'%';?>
                    <!-- </span> -->
                </div>
            </div>
            <div>
                <span class="text-xs text-gray-500">Monto Descuentos</span>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-lg text-red-500 font-bold ">
                        <?php echo '$ '.number_format($dyv['dyv']['año']['monto_reservas_con_descuento'], 0, ',', '.') ;?></span>
                    <span class="text-lg text-red-500 text-right pl-5">
                        <?php echo number_format($dyv['dyv']['año']['porcentaje_con_descuento'], 0, ',', '.').'%' ;?></span>
                </div>
            </div>
        </div>
    </div>

    <?php
    $SQL = "SELECT sucursales.sucursal FROM sucursales ORDER BY sucursales.posicion ASC";
    $sucursales = mysqli_query($con, $SQL);

    while ($sucursal = mysqli_fetch_array($sucursales)) { ?>
        <div class="flex flex-col gap-4 bg-white border shadow-md p-2 rounded-md border-gray-300 ">
            <div class="flex flex-col">
                <span class="text-lg font-bold text-gray-700 text-center"><?= $sucursal['sucursal']  ?></span>
                <div>
                    <span class="text-xs text-gray-500">Reservas</span>
                    <div class="flex justify-center items-center gap-2">
                        <span class="text-2xl text-green-500 font-bold pl-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['año']['cantidad_reservas'], 0, ',', '.') ;?></span>
                        <span class="text-xl text-blue-500 pr-5">
                            <?php // echo number_format(100, 0, ',', '.').'%';?>
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Reservas Con Descuento</span>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-xl text-red-500 font-bold pl-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['año']['cantidad_reservas_con_descuento'], 0, ',', '.') ;?></span>
                        <span class="text-xl text-red-500 pr-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['año']['porcentaje'], 0, ',', '.').'%' ;?></span>
                    </div>
                </div>
                <div class="my-2 border border-gray-300"></div>
                <div>
                    <span class="text-xs text-gray-500">Monto Operación</span>
                    <div class="flex justify-center items-center gap-2">
                        <span class="text-lg text-blue-500 font-bold text-center">
                            <?php echo '$ '.number_format($dyv[$sucursal['sucursal']]['año']['monto_reservas'], 0, ',', '.') ;?></span>
                        <!-- <span class="text-xl text-blue-500 "> -->
                            <?php // echo number_format(100, 0, ',', '.').'%';?>
                        <!-- </span> -->
                    </div>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Monto Descuentos</span>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-lg text-red-500 font-bold ">
                            <?php echo '$ '.number_format($dyv[$sucursal['sucursal']]['año']['monto_reservas_con_descuento'], 0, ',', '.') ;?></span>
                        <span class="text-lg text-red-500 text-right pl-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['año']['porcentaje_con_descuento'], 0, ',', '.').'%' ;?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</div>



<!-- Mes actual -->


<div class="p-2">
    <span class="text-lg font-bold text-gray-700"><?php echo "Mes ".nombre_del_mes($mes_actual); ?></span>
</div>
<div class="grid grid-cols-3 p-3 lg:grid-cols-5 gap-4" >
    <div class="flex flex-col gap-4 bg-white border shadow-md p-2 rounded-md border-gray-300 ">
        <div class="flex flex-col">
            <span class="text-lg font-bold text-gray-700 text-center">Derka y Vargas</span>
            <div>
                <span class="text-xs text-gray-500">Reservas</span>
                <div class="flex justify-center items-center gap-2">
                    <span class="text-2xl text-green-500 font-bold pl-5">
                        <?php echo number_format($dyv['dyv']['mes']['cantidad_reservas'], 0, ',', '.') ;?></span>
                    <span class="text-xl text-gray-700 pr-5">
                        <?php // echo number_format(100, 0, ',', '.').'%';?>
                    </span>
                </div>
            </div>
            <div>
                <span class="text-xs text-gray-500">Reservas Con Descuento</span>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-xl text-red-500 font-bold pl-5">
                        <?php echo number_format($dyv['dyv']['mes']['cantidad_reservas_con_descuento'], 0, ',', '.') ;?></span>
                    <span class="text-xl text-red-500 pr-5">
                        <?php echo number_format($dyv['dyv']['mes']['porcentaje'], 0, ',', '.').'%' ;?></span>
                </div>
            </div>
            <div class="my-2 border border-gray-300"></div>
            <div>
                <span class="text-xs text-gray-500">Monto Operación</span>
                <div class="flex justify-center items-center gap-2">
                    <span class="text-lg text-blue-500 font-bold text-center">
                        <?php echo '$ '.number_format($dyv['dyv']['mes']['monto_reservas'], 0, ',', '.') ;?></span>
                    <!-- <span class="text-xl text-blue-500 "> -->
                        <?php // echo number_format(100, 0, ',', '.').'%';?>
                    <!-- </span> -->
                </div>
            </div>
            <div>
                <span class="text-xs text-gray-500">Monto Descuentos</span>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-lg text-red-500 font-bold ">
                        <?php echo '$ '.number_format($dyv['dyv']['mes']['monto_reservas_con_descuento'], 0, ',', '.') ;?></span>
                    <span class="text-lg text-red-500 text-right pl-5">
                        <?php echo number_format($dyv['dyv']['mes']['porcentaje_con_descuento'], 0, ',', '.').'%' ;?></span>
                </div>
            </div>
        </div>
    </div>

    <?php
    $SQL = "SELECT sucursales.sucursal FROM sucursales ORDER BY sucursales.posicion ASC";
    $sucursales = mysqli_query($con, $SQL);

    while ($sucursal = mysqli_fetch_array($sucursales)) { ?>
        <div class="flex flex-col gap-4 bg-white border shadow-md p-2 rounded-md border-gray-300 ">
            <div class="flex flex-col">
                <span class="text-lg font-bold text-gray-700 text-center"><?= $sucursal['sucursal']  ?></span>
                <div>
                    <span class="text-xs text-gray-500">Reservas</span>
                    <div class="flex justify-center items-center gap-2">
                        <span class="text-2xl text-green-500 font-bold pl-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['mes']['cantidad_reservas'], 0, ',', '.') ;?></span>
                        <span class="text-xl text-blue-500 pr-5">
                            <?php // echo number_format(100, 0, ',', '.').'%';?>
                        </span>
                    </div>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Reservas Con Descuento</span>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-xl text-red-500 font-bold pl-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['mes']['cantidad_reservas_con_descuento'], 0, ',', '.') ;?></span>
                        <span class="text-xl text-red-500 pr-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['mes']['porcentaje'], 0, ',', '.').'%' ;?></span>
                    </div>
                </div>
                <div class="my-2 border border-gray-300"></div>
                <div>
                    <span class="text-xs text-gray-500">Monto Operación</span>
                    <div class="flex justify-center items-center gap-2">
                        <span class="text-lg text-blue-500 font-bold text-center">
                            <?php echo '$ '.number_format($dyv[$sucursal['sucursal']]['mes']['monto_reservas'], 0, ',', '.') ;?></span>
                        <!-- <span class="text-xl text-blue-500 "> -->
                            <?php // echo number_format(100, 0, ',', '.').'%';?>
                        <!-- </span> -->
                    </div>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Monto Descuentos</span>
                    <div class="flex justify-between items-center gap-2">
                        <span class="text-lg text-red-500 font-bold ">
                            <?php echo '$ '.number_format($dyv[$sucursal['sucursal']]['mes']['monto_reservas_con_descuento'], 0, ',', '.') ;?></span>
                        <span class="text-lg text-red-500 text-right pl-5">
                            <?php echo number_format($dyv[$sucursal['sucursal']]['mes']['porcentaje_con_descuento'], 0, ',', '.').'%' ;?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</div>

<div class="p-2">
    <span class="text-lg font-bold text-gray-700"><?php echo " Detalle Mes ".nombre_del_mes($mes_actual); ?></span>
</div>
<div class="mb-5">
    <table class="table-auto w-full border-collapse ">
        <thead class="text-gray-600 text-xs bg-white">
            <tr>
                <th rowspan="2" class="border border-gray-300 p-1">Sucursal</th>
                <th rowspan="2" class="border border-gray-300 p-1">Asesor</th>
                <!-- <th>Año</th>
                <th>Mes</th> -->
                <th colspan="6" class="border border-gray-300 p-1"><?php echo $año_actual; ?></th>
                <th></th>
                <th colspan="6" class="border border-gray-300 p-1"><?php echo nombre_del_mes($mes_actual); ?></th>
            </tr>
            <tr>
                <!-- <th>Año</th>
                <th>Mes</th> -->
                <th class="border border-gray-300 p-1">Rvas</th>
                <th class="border border-gray-300 p-1"> Desc.</th>
                <th class="border border-gray-300 p-1">%</th>
                <th class="border border-gray-300 p-1">$ Rvas</th>
                <th class="border border-gray-300 p-1">$ Desc.</th>
                <th class="border border-gray-300 p-1">% Desc.</th>
                <th class="p-1"></th>
                <th class="border border-gray-300 p-1">Rvas</th>
                <th class="border border-gray-300 p-1">Desc.</th>
                <th class="border border-gray-300 p-1">%</th>
                <th class="border border-gray-300 p-1">$ Rvas</th>
                <th class="border border-gray-300 p-1">$ Desc.</th>
                <th class="border border-gray-300 p-1">% Desc.</th>
            </tr>
        </thead>
        <tbody class="text-xs text-gray-700 bg-white">


            <?php
            $SQL = "SELECT sucursales.sucursal FROM sucursales ORDER BY sucursales.posicion ASC";
            $sucursales = mysqli_query($con, $SQL); ?>

            <?php
            while ($sucursal = mysqli_fetch_array($sucursales)) { ?>

                <tr>
                    <td colspan="15" class="p-3"></td>
                </tr>

                <?php $asesores_sucursal = $dyv[$sucursal['sucursal']]['asesores'];
                    $cant_asesores = count($asesores_sucursal);
                    foreach ($asesores_sucursal as $index => $asesor) { ?>
                    <tr class="text-center">
                        <?php if ($index == 0) { ?>
                            <td class="border border-gray-300" rowspan="<?php echo $cant_asesores; ?>"><?= $sucursal['sucursal'] ?></td>
                        <?php } ?>
                        <td class="border border-gray-300"><?= $asesor ?></td>
                        <!-- <td> - </td>
                        <td> - </td> -->
                        <td class="border border-gray-300 text-green-700"> <?php echo $dyv[$sucursal['sucursal']][$asesor]['año']['cantidad_reservas']; ?> </td>
                        <td class="border border-gray-300 text-orange-500"> <?php echo $dyv[$sucursal['sucursal']][$asesor]['año']['cantidad_reservas_con_descuento']; ?> </td>
                        <td class="border border-gray-300 text-orange-500"> <?php echo number_format($dyv[$sucursal['sucursal']][$asesor]['año']['porcentaje'], 0, ',', '.').'%'; ?> </td>
                        <td class="border border-gray-300 text-blue-700"> <?php echo '$ '.number_format($dyv[$sucursal['sucursal']][$asesor]['año']['monto_reservas'], 0, ',', '.'); ?> </td>
                        <td class="border border-gray-300 text-red-500"> <?php echo '$ '.number_format($dyv[$sucursal['sucursal']][$asesor]['año']['monto_reservas_con_descuento'], 0, ',', '.'); ?> </td>
                        <td class="border border-gray-300 text-red-500"> <?php echo number_format($dyv[$sucursal['sucursal']][$asesor]['año']['porcentaje_con_descuento'], 0, ',', '.').'%'; ?> </td>
                        <td class="p-1"></td>
                        <td class="border border-gray-300 text-green-500"> <?php echo $dyv[$sucursal['sucursal']][$asesor]['mes']['cantidad_reservas']; ?> </td>
                        <td class="border border-gray-300 text-orange-500"> <?php echo $dyv[$sucursal['sucursal']][$asesor]['mes']['cantidad_reservas_con_descuento']; ?> </td>
                        <td class="border border-gray-300 text-orange-500"> <?php echo number_format($dyv[$sucursal['sucursal']][$asesor]['mes']['porcentaje'], 0, ',', '.').'%'; ?> </td>
                        <td class="border border-gray-300 text-blue-700"> <?php echo '$ '.number_format($dyv[$sucursal['sucursal']][$asesor]['mes']['monto_reservas'], 0, ',', '.'); ?> </td>
                        <td class="border border-gray-300 text-red-500"> <?php echo '$ '.number_format($dyv[$sucursal['sucursal']][$asesor]['mes']['monto_reservas_con_descuento'], 0, ',', '.'); ?> </td>
                        <td class="border border-gray-300 text-red-500"> <?php echo number_format($dyv[$sucursal['sucursal']][$asesor]['mes']['porcentaje_con_descuento'], 0, ',', '.').'%'; ?> </td>

                    </tr>
                <?php } ?>

            <?php } ?>
        </tbody>
    </table>
</div>



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
