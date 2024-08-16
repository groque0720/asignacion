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
$planUuId = '';

if (isset($_GET['id'])) {
    $planUuId = $_GET['id']; 
    $SQL = "SELECT
            tpa_planes_avanzados.*, 
            tpa_modalidades.modalidad, 
            tpa_planes_versiones.version, 
            tpa_planes_modelos.modelo
        FROM
            tpa_planes_avanzados
            INNER JOIN
            tpa_planes_versiones
            ON 
                tpa_planes_avanzados.version_id = tpa_planes_versiones.id
            INNER JOIN
            tpa_planes_modelos
            ON 
                tpa_planes_versiones.modelo_id = tpa_planes_modelos.id
            INNER JOIN
            tpa_modalidades
            ON 
                tpa_planes_avanzados.modalidad_id = tpa_modalidades.id
        WHERE
            tpa_planes_avanzados.uuid = '$planUuId'";

    $result = mysqli_query($con, $SQL);
    $plan = mysqli_fetch_array($result);

    if (!$plan) {
        include("layouts/error_plan_no_encontrado.php");
        die();
    }

    if($plan['estado_id'] != 1) {
        $grupo_orden = $plan['grupo_orden'];
        include("layouts/error_plan_ya_reservado.php");
        die();
    }

}







?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php
        $title = "Reserva Plan Avanzado";
        include("components/header.php");
    ?>
</head>
<body>
    <div class="container m-auto ">
        <?php
            $titulo = "Reserva Plan Avanzado";
            include("components/cabecera.php");
        ?>

        <form class=" mx-auto p-5 border rounded" autocomplete="off" action="actions/reservar_plan_avanzado.php" method="POST" >

        <input type="text" id="planUuId" class="p-2 text-right pr-4" name="planUuId" value="<?php echo $planUuId ? $plan['uuid']:'';  ?>" hidden  />
        <input type="date" id="planUuId" class="p-2 text-right pr-4" name="fecha_reserva" value="<?php echo date("Y-m-d");  ?>"  hidden  />
        <input type="time" id="planUuId" class="p-2 text-right pr-4" name="hora_reserva" value="<?php echo date("H:i");  ?>"  hidden  />
        <!-- <input type="text" id="planUuId" class="p-2 text-right pr-4" name="planUuId" value="" hidden  /> -->

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">

                <div class="mb-5">
                    <label for="modelo">Modelo - Versión</label>
                    <div class="input_info"><?php echo $plan['modelo'] ?></div>
                </div>


                <div class="mb-5">
                    <label for="modalidad">Modalidad - Plazo</label>
                    <div class="input_info"><?php echo $plan['modalidad'] ?></div>
                </div>
                
                <div class="mb-5">
                    <label for="grupo_orden" >Grupo - Orden</label>
                    <div class="input_info text-right"><?php echo $plan['grupo_orden'] ?></div>
                </div>

                <div class="mb-5">
                    <label for="cuotas_pagadas_cantidad" >Cantidad Cuotas Pagadas </label>
                    <div class="input_info text-right"><?php echo $plan['cuotas_pagadas_cantidad'] ?></div>
                </div>

                <div class="mb-5">
                    <label for="cuota_promedio" >Cuota Promedio</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['cuota_promedio'], 2, ',', '.'); ?></div>
                </div>

                <div class="mb-5">
                    <label for="valor_unidad" >Valor Unidad</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['valor_unidad'], 2, ',', '.'); ?></div>
                </div>
                <div class="mb-5">
                    <label for="integracion" >Integración</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['integracion'], 2, ',', '.'); ?> </div>
                </div>
                <div class="mb-5">
                    <label for="derecho_adjudicacion" >Derecho Adjudicación</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['derecho_adjudicacion'], 2, ',', '.'); ?></div>
                </div>

                <div class="mb-5">
                    <label for="venta" >Precio Venta</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['venta'], 2, ',', '.'); ?></div>
                </div>
              
            </div>
            <hr class="mb-5">
            <!-- INformación de la reserva -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <div class="mb-5">
                    <label for="estado">Estado</label>
                    <select id="estado" class="p-2" name="estado" required  disabled>
                        <option value=""></option>
                        <?php
                            include("actions/obtener_estados.php");
                            while ($estado=mysqli_fetch_array($estados)) {
                                $selected = ($estado['id'] == 2) ? 'selected' : '';
                                echo "<option value='".$estado['id']."' $selected>".$estado['estado']."</option>";
                            };  
                        ?>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="usuario_venta_id">Asesor Venta</label>
                    <select id="usuario_venta_id" class="p-2 " name="usuario_venta_id" disabled required>
                        <option value="null"></option>
                        <?php
                            include("actions/obtener_usuario_asesores.php");
                            while ($usuario=mysqli_fetch_array($usuarios)) {
                                $selected = ($usuario['idusuario'] == $userId) ? 'selected' : '';
                                echo "<option value='".$usuario['idusuario']."' $selected>".$usuario['nombre']."</option>";
                            };  
                        ?>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="monto_reserva" class="text-red-600" >Monto Reserva <sup>(*)</label>
                    <input type="text" id="monto_reserva" name="monto_reserva" required  value="<?php echo $planUuId ? $plan['monto_reserva']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="modelo_version_retirar" class="text-red-600" >Modelo Versión Final <sup>(*)</label>
                    <input type="text" id="modelo_version_retirar" name="modelo_version_retirar" required  value="<?php echo $plan['modelo_version_retirar'];  ?>" class="p-2 text-right pr-4"  />
                </div>
 
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <div class="mb-5">
                    <label for="cliente " class="text-red-600" >Cliente <sup>(*)</sup></label>
                    <input type="text" id="cliente" name="cliente" required value="<?php echo $planUuId ? $plan['cliente']:'';  ?>" class="p-2 text-right pr-4"   />
                </div>
                <div class="mb-5">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" class="p-2 " name="sexo" required>
                        <option value=""></option>
                        <option value="M" <?php echo $planUuId && $plan['sexo'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo $planUuId && $plan['sexo'] == 'F' ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="fecha_nacimiento" >Fecha Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="<?php echo $planUuId ? $plan['fecha_nacimiento']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="edad" >Edad</label>
                    <input type="text" id="edad" name="edad" required value="<?php echo $planUuId ? $plan['edad']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="dni" >DNI</label>
                    <input type="text" id="dni" name="dni" required value="<?php echo $planUuId ? $plan['dni']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="cuil" >CUIL / CUIT</label>
                    <input type="text" id="cuil" name="cuil" required value="<?php echo $planUuId ? $plan['cuil']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="direccion" >Dirección</label>
                    <input type="text" id="direccion" name="direccion" required value="<?php echo $planUuId ? $plan['direccion']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="localidad" >Localidad</label>
                    <input type="text" id="localidad" name="localidad" required value="<?php echo $planUuId ? $plan['localidad']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="provincia" >Provincia</label>
                    <input type="text" id="provincia" name="provincia" required value="<?php echo $planUuId ? $plan['provincia']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="email" >Email</label>
                    <input type="text" id="email" name="email" required value="<?php echo $planUuId ? $plan['email']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="mb-5">
                    <label for="celular" >Celular</label>
                    <input type="text" id="celular" name="celular" required value="<?php echo $planUuId ? $plan['celular']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>

            </div>
            <div class="flex justify-end border-t pt-5">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Reservar</button>
            </div>

        </form>

    </div>

</body>
<?php
    mysqli_close($con);
?>
</html>