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

    $modelo_activo_id=$_GET['modelo_activo'] ?? 1;
    $situacionId = $_GET['situacionId'] ?? 1;

    include("actions/obtener_modelo_activo.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php
        $title = $situacionId ==1 ? "Avanzados - ".$modelo_activo_nombre : "Adjudicados - ".$modelo_activo_nombre ;
        include("components/header.php");
    ?>
</head>
<body>

    <div class="container m-auto ">

        <?php
            $titulo = $situacionId == 1 ? "Listado de planes - avanzados - ".$modelo_activo_nombre : "Listado de planes - adjudicados - ".$modelo_activo_nombre ;
            include("components/cabecera.php");
        ?>

        <!-- Botonera seleccion de situacion -->
        <div class="flex justify-between mb-3">
            <?php 
                $situ1="bg-gray-500 text-white p-2 px-5 rounded";
                $situ2="bg-gray-100 text-gray-400 p-2 px-5 rounded";
                if($situacionId == 2) {
                    $situ1="bg-gray-100 text-gray-400 p-2 px-5 rounded";
                    $situ2="bg-gray-500 text-white p-2 px-5 rounded";
                }
            ?>
            <div class="flex gap-2">
                <a href="?situacionId=1&modelo_activo=<?php echo $modelo_activo_id; ?>">
                    <Button class="<?php echo $situ1 ?>">Avanzados</Button>
                </a>
                <a href="?situacionId=2&modelo_activo=<?php echo $modelo_activo_id; ?>">
                    <button class="<?php echo $situ2 ?>">Adjudicados</button>
                </a>
            </div>
            <?php if($isAdmin) { ?>
                <a
                href="/planes_avanzados/plan_view.php" 
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Nuevo
                </a>
            <?php } ?>
        </div>

        <!-- Botonera Seleccion de Modelos -->
        <div class="flex mb-3 gap-3">
            <?php
                $mod_active="bg-blue-500 text-white p-2 px-5 rounded";
                $mod_inactive="bg-blue-100 text-blue-400 p-2 px-5 rounded";
            ?>

            <?php while($modelo=mysqli_fetch_array($modelos_activos) ) { ?>
                <a href="?situacionId=<?php echo $situacionId;  ?>&modelo_activo=<?php echo $modelo['modelo_id']; ?>">
                    <Button class="<?php echo $modelo_activo_id == $modelo['modelo_id'] ? $mod_active : $mod_inactive ?>"><?php echo $modelo['modelo']  ?></Button>
                </a>
            <?php } ?>
        </div>
        

        <div class="zona-tabla m-auto mb-64">


                      
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
                        <th rowspan="2">$ Venta</th>
                        <th rowspan="2">Cuota Promedio</th>
                        <th rowspan="2">Valor Unidad</th>
                        <?php if($isAdmin) { ?>
                            <th rowspan="2">Reserva</th>  
                        <?php } ?>
                        <th rowspan="2">Situación</th>
                        
                    </tr>
                    <tr>
                        <!-- <tr></tr>
                        <tr></tr>
                        <tr></tr> -->
                        <th>Cantidad</th>
                        <?php if($isAdmin) { ?>
                            <th>Monto <sup>(*)</sup></th>
                        <?php } ?>
                            
                    </tr>
                </thead>
                <tbody class="tbody_tpa">


                    <?php
                    include("actions/obtener_planes_avanzados.php");
                    while($plan=mysqli_fetch_array($planes_avanzados)  ) { ?>
                    <tr>
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
                            <td class="td_right"> <?php echo '$ '.number_format($plan['cuotas_pagadas_monto'], 2, ',', '.'); ?></td>
                            <td class="td_right"> <?php echo '$ '.number_format($plan['costo'], 2, ',', '.'); ?></td>
                            <td class="td_right td_red"> <?php echo '$ '.number_format($plan['plus'], 2, ',', '.'); ?></td>
                        <?php } ?>
                        <td class="td_right "> <?php echo '$ '.number_format($plan['venta'], 2, ',', '.'); ?></td>
                        <td class="td_right"> <?php echo '$ '.number_format($plan['cuota_promedio'], 2, ',', '.'); ?></td>
                        <td class="td_right"> <?php echo '$ '.number_format($plan['valor_unidad'], 2, ',', '.'); ?></td>
                        <?php if($isAdmin) { ?>
                            <td class="td_right"> <?php echo '$ '.number_format($plan['monto_reserva'], 2, ',', '.'); ?></td>
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