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
            <div class="flex gap-3">
                <a
                href="/planes_avanzados/?situacionId=<?php echo $situacionId;  ?>&modelo_activo=<?php echo $modelo_activo_id; ?>" 
                class="">
                    <svg fill="#656ef1" height="40px" width="40px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 496 496" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M456,64H40C17.944,64,0,81.944,0,104v288c0,22.056,17.944,40,40,40h416c22.056,0,40-17.944,40-40V104 C496,81.944,478.056,64,456,64z M21.124,406.796C17.919,402.716,16,397.579,16,392v-8h43.117L21.124,406.796z M240,144v32h-64v-32 H240z M320,144v32h-64v-32H320z M400,144v32h-64v-32H400z M480,144v32h-64v-32H480z M80,272H36.883L80,246.13V272z M96,240h64v32 H96V240z M96,288h64v32H96V288z M80,320H36.883L80,294.13V320z M176,288h64v32h-64V288z M176,272v-32h64v32H176z M256,240h64v32 h-64V240z M336,240h64v32h-64V240z M336,224v-32h64v32H336z M320,224h-64v-32h64V224z M240,224h-64v-32h64V224z M160,224H96v-32h64 V224z M80,224H36.883L80,198.13V224z M16,192h43.117L16,217.87V192z M36.883,176L80,150.13V176H36.883z M16,240h43.117L16,265.87 V240z M16,288h43.117L16,313.87V288z M16,336h43.117L16,361.87V336z M80,342.13V368H36.883L80,342.13z M96,336h64v32H96V336z M176,336h64v32h-64V336z M256,336h64v32h-64V336z M256,320v-32h64v32H256z M336,288h64v32h-64V288z M416,288h64v32h-64V288z M416,272v-32h64v32H416z M416,224v-32h64v32H416z M160,176H96v-32h64V176z M16,169.87V144h43.117L16,169.87z M160,384v32H96v-32 H160z M176,384h64v32h-64V384z M256,384h64v32h-64V384z M336,384h64v32h-64V384z M336,368v-32h64v32H336z M416,336h64v32h-64V336z M40,80h416c13.234,0,24,10.766,24,24v24H16v-24C16,90.766,26.766,80,40,80z M37.17,415.828L80,390.129V416H40 C39.042,416,38.099,415.937,37.17,415.828z M456,416h-40v-32h64v8C480,405.234,469.234,416,456,416z"></path> <path d="M40,112h16c4.418,0,8-3.582,8-8s-3.582-8-8-8H40c-4.418,0-8,3.582-8,8S35.582,112,40,112z"></path> <path d="M120,112h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S115.582,112,120,112z"></path> <path d="M200,112h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S195.582,112,200,112z"></path> <path d="M280,112h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S275.582,112,280,112z"></path> <path d="M280,168h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S275.582,168,280,168z"></path> <path d="M280,216h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S275.582,216,280,216z"></path> <path d="M216,248h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S220.418,248,216,248z"></path> <path d="M376,296h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S380.418,296,376,296z"></path> <path d="M136,296h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S140.418,296,136,296z"></path> <path d="M136,344h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S140.418,344,136,344z"></path> <path d="M296,344h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S300.418,344,296,344z"></path> <path d="M456,200h-16c-4.418,0-8,3.582-8,8s3.582,8,8,8h16c4.418,0,8-3.582,8-8S460.418,200,456,200z"></path> <path d="M360,112h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S355.582,112,360,112z"></path> <path d="M440,112h16c4.418,0,8-3.582,8-8s-3.582-8-8-8h-16c-4.418,0-8,3.582-8,8S435.582,112,440,112z"></path> </g> </g></svg>
                </a>
                <?php if($isAdmin) { ?>
                    <a
                    href="/planes_avanzados/plan_view.php" 
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Nuevo
                    </a>
                <?php } ?>                
            </div>
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

        <?php include("components/marcador_estado.php"); ?>
        

        <div class="zona-tabla m-auto mb-64">

            <?php

                include("actions/obtener_planes_avanzados_x_sit_mod.php"); ?>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">

                    <?php while($plan=mysqli_fetch_array($planes_avanzados)  ) { ?>

                        <div class="border rounded  flex flex-col "> 
                            <div class="flex flex-col gap-1 p-2 items-center border-b <?php echo $plan['estado_id'] == 1 ?  'bg-green-50' : ''?> relative">
                                <span class="text-sm font-bold"><?php echo $plan['modelo'].' '.$plan['version']; ?></span>                                  
                                <span class="text-sm font-bold"><?php echo $plan['modalidad']; ?></span>
                                <div class="absolute top-5 right-3">
                                    <svg width="18" height="18" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="12" fill="<?php echo $plan['estado_id'] == 1 ? '#abebc6':'#fad7a0'; ?>"/>
                                    </svg>                             
                                </div>     
                            </div>

                            <!-- <div class="flex flex-col items-center text-sm <?php echo $plan['estado_id'] == 1 ?  'bg-gray-50' : ''?>"> -->
                            <div class="flex flex-col items-center text-sm bg-gray-50">
                                <div class="border-b w-full">
                                     <?php if($isAdmin) { ?>
                                        <div class="underline p-1 pl-2 text-center text-blue-500">
                                <!-- <div class="p-1 pl-2 text-center">Grupo y Orden: <?php echo $plan['grupo_orden']; ?></div> -->
                                            <a href="/planes_avanzados/plan_view.php?id=<?php echo $plan['uuid'] ;?>">Grupo y Orden: <?php echo $plan['grupo_orden']; ?></a>
                                        </div>
                                    <?php } else { ?>
                                        <div class="p-1 pl-2 text-center">Grupo y Orden: <?php echo $plan['grupo_orden']; ?></div>
                                     <?php } ?>
                                    
                                    <!-- <div class="p-1 pl-2 text-center">Grupo y Orden: <?php echo $plan['grupo_orden']; ?></div> -->
                                    <!-- <div class="flex-1 border-l p-1 text-right pr-2"></div> -->
                               </div>
                               <div class="w-full p-1 bg-white"></div>

                               <div class="flex border-b border-t w-full">
                                    <div class="w-7/12  p-1 pl-2">Cuotas Pagas <span class="text-red-600 font-bold">(<?php echo $plan['cuotas_pagadas_cantidad']; ?>)</span></div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['cuotas_pagadas_monto'], 2, ',', '.'); ?></div>
                               </div>
                               <div class="flex border-b w-full">
                                    <div class="w-7/12  p-1 pl-2">Costo DYV</div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['costo'], 2, ',', '.'); ?></div>
                               </div>
                               <div class="flex border-b w-full">
                                    <div class="w-7/12  p-1 pl-2"><span class="text-red-600">Plus</span></div>
                                    <div class="flex-1 border-l p-1 text-right pr-2 text-red-600"><?php echo ''.number_format($plan['plus'], 2, ',', '.'); ?></div>
                               </div>
                               <div class="flex border-b w-full">
                                    <div class="w-7/12  p-1 pl-2 font-bold">Precio Venta </div>
                                    <div class="flex-1 border-l p-1 text-right pr-2 font-bold"><?php echo ''.number_format($plan['venta'], 2, ',', '.'); ?></div>
                               </div>

                               <div class="w-full p-1 bg-white"></div>

                               <div class="flex border-b border-t w-full text-red-600">
                                    <div class="w-7/12  p-1 pl-2">Cuota Promedio </div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['cuota_promedio'], 2, ',', '.'); ?></div>
                               </div>
                               <div class="flex w-full border-b">
                                    <div class="w-7/12  p-1 pl-2">Valor de la unidad</div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['valor_unidad'], 2, ',', '.'); ?></div>
                               </div>

                               <div class="w-full p-1 bg-white"></div>

                                <div class="flex border-b border-t w-full">
                                    <div class="w-7/12  p-1 pl-2">Integración </div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['integracion'], 2, ',', '.'); ?></div>
                                </div>
                                <div class="flex w-full border-b">
                                    <div class="w-7/12  p-1 pl-2">Derecho Adjudicación</div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['derecho_adjudicacion'], 2, ',', '.'); ?></div>
                                </div>
                                <div class="flex border-b w-full font-bold">
                                    <div class="w-7/12  p-1 pl-2">Total </div>
                                    <div class="flex-1 border-l p-1 text-right pr-2 "><?php echo ''.number_format($plan['precio_final'], 2, ',', '.'); ?></div>
                                </div>

                                <div class="w-full p-1 bg-white"></div>
                                <div class="flex w-full border-b border-t">
                                    <div class="w-7/12  p-1 pl-2">Reserva</div>
                                    <div class="flex-1 border-l p-1 text-right pr-2"><?php echo ''.number_format($plan['monto_reserva'], 2, ',', '.'); ?></div>
                                </div>

                               <div class="w-full p-1 bg-white"></div>

                               <div class="flex w-full border-t">
                                    <div class="w-full  p-1 pr-2 text-right">
                                    <?php if($plan['estado_id'] == 1 OR $userId == $plan['usuario_venta_id']) {  ?>
                                            <!-- Si es el mismo usuario que lo reservo puede ir a editar el plan -->
                                            <?php if($userId == $plan['usuario_venta_id']) { ?>
                                                <a href="plan_reservar.php?id=<?php echo $plan['uuid'] ?>">
                                                    <div class="flex w-full justify-end">
                                                        <span class="text-left"><?php echo $plan['cliente']. ' / '  ?></span>
                                                        <span class="text-left text-blue-600"><?php echo $plan['usuario_venta']  ?></span>
                                                    </div>
                                                </a>
                                            <?php }else { ?> 
                                                <!-- si el plan esta libre -->
                                                <a href="plan_reservar.php?id=<?php echo $plan['uuid'] ?>" class="text-green-500">Reservar</a>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <div class="flex w-full justify-end">
                                                <span class="text-left"><?php echo $plan['cliente']. ' / '  ?></span>
                                                <span class="text-left text-blue-600"><?php echo $plan['usuario_venta']  ?></span>
                                                <!-- <hr>
                                                <span class="text-gray-500"><?php echo $plan['usuario_venta'] ?></span> -->
                                            </div>
                                    <?php } ?> 
                                    </div>
                               </div>


                            </div>

                        </div>

                    <?php } ?>
                
                </div>

        </div>



    </div>




</body>
</html>