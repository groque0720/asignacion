<?php

    include("funciones/func_mysql.php");
    conectar();
    mysqli_query($con,"SET NAMES 'utf8'");
    @session_start();
    //COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
    if ($_SESSION["autentificado"] != "SI") {
        //si no existe, envio a la página de autentificacion
        header("Location: ../login");
        //ademas salgo de este script
        exit();
    }
    $userId = $_SESSION["id"];
    $usersAdmin = ['56','81','11'];
    // 56 Mauro Vargas
    // 81 Santiago Galiano
    // 11 Admin
    $isAdmin = in_array($userId, $usersAdmin);

    include("actions/obtener_modelos_activos_en_planes.php");
    include("actions/obtener_situacion.php");


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php
        $title = "Listado de Planes Activos";
        include("components/header.php");
    ?>
</head>
<body>

    <div class="container m-auto ">

        <?php  
            $titulo = "Listado de Planes Activos";
            include("components/cabecera.php");
        ?>

        <div class="zona-tabla m-auto mb-64">



            <?php

                include("actions/obtener_planes_avanzados_all.php"); ?>
         
                <table class="table_tpa w-full text-xs ">
                    <thead class="thead_tpa">
                        <tr>
                            <th rowspan="2">Plan</th>
                            <th rowspan="2">Modalidad</th>
                            <th rowspan="2">Grupo-Orden</th>
                            <th colspan="<?php echo  $isAdmin ? 2 : 1  ?>">Cuotas Pagadas</th>
                            <?php if($isAdmin) { ?>
                                <th rowspan="2">Costo <sup>(*)</sup></th>
                                <th rowspan="2" ><span class="td_red">Plus <sup>(*)</sup></span></th>
                            <?php } ?>
                            <th rowspan="2">Cuota Promedio</th>
                            <th rowspan="2">Valor Unidad</th>
                            <th rowspan="2">Venta</th>
                            <th rowspan="2">Integración</th>
                            <th rowspan="2">Derecho de <br> Adjudicación</th>
                            <th rowspan="2" > <span class="text-red-600 font-bold">Total</span></th>
                            <?php if($isAdmin) { ?>
                                <th rowspan="2">Reserva</th>  
                            <?php } ?>
                            <th rowspan="2">Situación <br> <span class="text-xs font-normal">Cliente/asesor</span> </th>
                            
                        </tr>
                        <tr>
                            <th>Cantidad</th>
                            <?php if($isAdmin) { ?>
                                <th>Monto <sup>(*)</sup></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody class="tbody_tpa">
                        <?php
                         $situacion = '';
                        while($plan=mysqli_fetch_array($planes_avanzados)  ) { ?>

                        <?php if($situacion == '' || $situacion != $plan['situacion']) { $situacion = $plan['situacion']; ?>
                            <tr>
                                <td colspan="20" class="text-center"> <span class="font-bold uppercase"><?php echo $plan['situacion'] ?> </span></td>
                            </tr>
                        <?php } ?>

                        <tr class="<?php echo $plan['estado_id'] == 1 ?  'bg-green-50' : ''?>">
                            <td class="td_bold"><?php echo $plan['modelo'].' '.$plan['version']; ?></td>
                            <td class="td_center td_bold"><?php echo $plan['modalidad']; ?></td>
                            <td class="td_center  td_blue" >
                                <?php if($isAdmin) { ?>
                                    <div class="underline">
                                        <a href="/planes_avanzados/plan_view.php?id=<?php echo $plan['uuid'] ;?>"><?php echo $plan['grupo_orden']; ?></a>
                                    </div>
                                <?php } else { ?>
                                    <?php echo $plan['grupo_orden']; ?>
                                <?php } ?>
                            </td>
                            <td class="td_center"><?php echo $plan['cuotas_pagadas_cantidad']; ?></td>
                            <?php if($isAdmin) { ?>
                                <td class="td_right"> <?php echo ''.number_format($plan['cuotas_pagadas_monto'], 2, ',', '.'); ?></td>
                                <td class="td_right"> <?php echo ''.number_format($plan['costo'], 2, ',', '.'); ?></td>
                                <td class="td_right td_red"> <?php echo ''.number_format($plan['plus'], 2, ',', '.'); ?></td>
                            <?php } ?>
                            <td class="td_right"> <?php echo ''.number_format($plan['cuota_promedio'], 2, ',', '.'); ?></td>
                            <td class="td_right"> <?php echo ''.number_format($plan['valor_unidad'], 2, ',', '.'); ?></td>
                            <td class="td_right "> <?php echo ''.number_format($plan['venta'], 2, ',', '.'); ?></td>
                            <td class="td_right "> <?php echo ''.number_format($plan['integracion'], 2, ',', '.'); ?></td>
                            <td class="td_right "> <?php echo ''.number_format($plan['derecho_adjudicacion'], 2, ',', '.'); ?></td>
                            <td class="td_right text-red-600 font-bold"> <?php echo ''.number_format($plan['precio_final'], 2, ',', '.'); ?></td>
                            <?php if($isAdmin) { ?>
                                <td class="td_right"> <?php echo ''.number_format($plan['monto_reserva'], 2, ',', '.'); ?></td>
                            <?php } ?>
                            <td class="td_right">
                                <div class="flex items-center pl-3 gap-5">
                                    <?php
                                        switch ($plan['estado_id']) {
                                            case 1:
                                                $color = '#abebc6';
                                                break;
                                            case 2:
                                                $color = '#fad7a0';
                                                break;
                                            case 3:
                                                $color = '#f1948a';
                                                break;
                                            default:
                                                $color = 'black';
                                                break;
                                        }
                                    ?>
                                    <svg width="18" height="18" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="12" fill="<?php echo $color; ?>"/>
                                    </svg>

                                    <?php if($plan['estado_id'] == 1 OR $userId == $plan['usuario_venta_id']) {  ?>
                                        <!-- Si es el mismo usuario que lo reservo puede ir a editar el plan -->
                                        <?php if($userId == $plan['usuario_venta_id']) { ?>
                                            <a href="plan_reservar.php?id=<?php echo $plan['uuid'] ?>">
                                                <div class="flex w-full">
                                                    <span class="text-left"><?php echo $plan['cliente']. ' / '  ?></span>
                                                    <span class="text-left text-blue-600"><?php echo $plan['usuario_venta']  ?></span>
                                                </div>
                                            </a>
                                        <?php }else { ?> 
                                            <!-- si el plan esta libre -->
                                            <a href="plan_reservar.php?id=<?php echo $plan['uuid'] ?>" class="text-green-500">reservar</a>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <div class="flex w-full">
                                            <span class="text-left"><?php echo $plan['cliente']. ' / '  ?></span>
                                            <span class="text-left text-blue-600"><?php echo $plan['usuario_venta']  ?></span>
                                            <!-- <hr>
                                            <span class="text-gray-500"><?php echo $plan['usuario_venta'] ?></span> -->
                                        </div>
                                    <?php } ?> 
                                </div>
                            </td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table> 

            


        </div>
    </div>




</body>
</html>