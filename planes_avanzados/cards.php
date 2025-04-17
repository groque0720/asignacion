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
    $estadoId = $_GET['estadoId'] ?? null;

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
                    <svg width="40px" height="40px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 6.00067L21 6.00139M8 12.0007L21 12.0015M8 18.0007L21 18.0015M3.5 6H3.51M3.5 12H3.51M3.5 18H3.51M4 6C4 6.27614 3.77614 6.5 3.5 6.5C3.22386 6.5 3 6.27614 3 6C3 5.72386 3.22386 5.5 3.5 5.5C3.77614 5.5 4 5.72386 4 6ZM4 12C4 12.2761 3.77614 12.5 3.5 12.5C3.22386 12.5 3 12.2761 3 12C3 11.7239 3.22386 11.5 3.5 11.5C3.77614 11.5 4 11.7239 4 12ZM4 18C4 18.2761 3.77614 18.5 3.5 18.5C3.22386 18.5 3 18.2761 3 18C3 17.7239 3.22386 17.5 3.5 17.5C3.77614 17.5 4 17.7239 4 18Z" stroke="#628cdf" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </a>
                <?php if($isAdmin) { ?>
                    <select id="exportarExcel" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <option value="">Exportar en Excel</option>
                        <option value="todos">Exportar Libres y Reservados</option>
                        <option value="libres">Exportar Solo Libres</option>
                        <option value="reservados">Exportar Solo Reservados</option>
                    </select>
                    <a href="/planes_avanzados/exportar_todo.php?situacionId=<?php echo $situacionId; ?>&formato=cards" 
                       class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap">
                        Exportar Todo
                    </a>
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
                               
                               <?php if($isAdmin) { ?>

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
                               <?php } ?>
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



    <script>
        document.getElementById('exportarExcel').addEventListener('change', function() {
            const tipo = this.value;
            if (!tipo) return;
            
            let url = '';
            let params = '?situacionId=<?php echo $situacionId; ?>&modelo_activo=<?php echo $modelo_activo_id; ?>';
            
            switch(tipo) {
                case 'todos':
                    url = '/planes_avanzados/exportar.php' + params;
                    break;
                case 'libres':
                    url = '/planes_avanzados/exportar.php' + params + '&estadoId=1';
                    break;
                case 'reservados':
                    url = '/planes_avanzados/exportar.php' + params + '&estadoId=2';
                    break;
            }
            
            if (url) {
                window.location.href = url;
            }
        });
    </script>
</body>
</html>