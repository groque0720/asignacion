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

$planUuId = '';

if (isset($_GET['id'])) {
    $planUuId = $_GET['id']; 
    $SQL = "SELECT * FROM tpa_planes_avanzados WHERE uuid = '$planUuId'";
    $result = mysqli_query($con, $SQL);
    $plan = mysqli_fetch_array($result);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php
        $title = "Plan Avanzado";
        include("components/header.php");
    ?>
</head>
<body>
    <div class="container m-auto ">
        <?php
            $titulo = "Plan Avanzado";
            include("components/cabecera.php");
        ?>

        <form class=" mx-auto p-5 border rounded" autocomplete="off" action="actions/crear_plan_avanzado.php" method="POST" >

        <input type="text" id="planUuId" class="p-2 text-right pr-4" name="planUuId" value="<?php echo $planUuId ? $plan['uuid']:'';  ?>" hidden  />
        <input type="text" name="situacionIdActual" value="<?php echo $plan['situacion_id'] ?? 1; ?>" hidden>
        <!-- <input type="text" id="planUuId" class="p-2 text-right pr-4" name="planUuId" value="" hidden  /> -->

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">


                <div class="mb-5">
                    <label for="modelo">Modelo - Versión</label>
                    <select id="modelo" class="p-2" name="modelo" required>
                        <option value=""></option>
                        <?php
                            include("actions/obtener_modelos.php");
                            while ($modelo=mysqli_fetch_array($modelos)) {
                                $selected = ($modelo['id'] == $plan['modelo_id']) ? 'selected' : '';
                                echo "<option value='".$modelo['id']."' $selected>".$modelo['modelo']."</option>";
                                // echo "<option value='".$modelo['id']."'>".$modelo['modelo']."</option>";
                            };  
                        ?>
                    </select>
                </div>


                <div class="mb-5">
                    <label for="modalidad">Modalidad - Plazo</label>
                    <select id="modalidad" class="p-2 " name="modalidad" required >
                        <option value=""></option>
                        <?php
                            include("actions/obtener_modalidades.php");
                            while ($modalidad=mysqli_fetch_array($modalidades)) {
                                $selected = ($modalidad['id'] == $plan['modalidad_id']) ? 'selected' : '';
                                echo "<option value='".$modalidad['id']."' $selected>".$modalidad['modalidad']."</option>";
                            };  
                        ?>
                    </select>
                </div>
                
                <div class="mb-5">
                    <label for="grupo_orden" >Grupo - Orden</label>
                    <input type="text" id="grupo_orden" class="p-2 text-right pr-4" name="grupo_orden" value="<?php echo $planUuId ? $plan['grupo_orden']:'';  ?>" required />
                </div>

                <div class="mb-5">
                    <label for="situacion_id">Situación Plan</label>
                    <select id="situacion_id" class="p-2" name="situacion_id" required>
                        <option value=""></option>
                        <?php
                            include("actions/obtener_situacion.php");
                            while ($situacion=mysqli_fetch_array($situaciones)) {
                                $selected = ($situacion['id'] == $plan['situacion_id']) ? 'selected' : '';
                                echo "<option value='".$situacion['id']."' $selected>".$situacion['situacion']."</option>";
                                // echo "<option value='".$modelo['id']."'>".$modelo['modelo']."</option>";
                            };  
                        ?>
                    </select>
                </div>

                <div class="mb-5">
                    <label for="cuotas_pagadas_cantidad" >Cantidad Cuotas Pagadas </label>
                    <input type="text" id="cuotas_pagadas_cantidad" name="cuotas_pagadas_cantidad" value="<?php echo $planUuId ? $plan['cuotas_pagadas_cantidad']:'';  ?>" required  class="p-2 text-right pr-4"  />
                </div>

                <div class="mb-5">
                    <label for="cuotas_pagadas_monto" >Monto Cuotas Pagadas</label>
                    <input type="text" id="cuotas_pagadas_monto" name="cuotas_pagadas_monto" value="<?php echo $planUuId ? $plan['cuotas_pagadas_monto']:'';  ?>" required  class="p-2 text-right pr-4" value=""  />
                </div>

                <div class="mb-5">
                    <label for="costo" >Costo DYV</label>
                    <input type="text" id="costo" name="costo"  class="p-2 text-right pr-4" value="<?php echo $planUuId ? $plan['costo']:'';  ?>" required value=""  />
                </div>

                <div class="mb-5">
                    <label for="plus" class="text-red-600" >Plus</label>
                    <input type="text" id="plus" name="plus"  class="p-2 text-right pr-4 text-red-600" value="<?php echo $planUuId ? $plan['plus']:'';  ?>"  required value=""  />
                </div>

                <div class="mb-5">
                    <label for="venta" >Precio Venta</label>
                    <input type="text" id="venta"  name="venta"  class="p-2 text-right pr-4" value="<?php echo $planUuId ? $plan['venta']:'';  ?>" required value=""  />
                </div>

                <div class="mb-5">
                    <label for="cuota_promedio" >Cuota Promedio</label>
                    <input type="text" id="cuota_promedio" name="cuota_promedio" required value="<?php echo $planUuId ? $plan['cuota_promedio']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>

                <div class="mb-5">
                    <label for="valor_unidad" >Valor Unidad</label>
                    <input type="text" id="valor_unidad" name="valor_unidad" required value="<?php echo $planUuId ? $plan['valor_unidad']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>

                <div class="mb-5">
                    <label for="integracion" >Integración</label>
                    <input type="text" id="integracion" name="integracion" required value="<?php echo $planUuId ? $plan['integracion']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>

                <div class="mb-5">
                    <label for="derecho_adjudicacion" >Derecho de Adjudicación</label>
                    <input type="text" id="derecho_adjudicacion" name="derecho_adjudicacion" required value="<?php echo $planUuId ? $plan['derecho_adjudicacion']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>


                
            </div>
            <hr class="mb-5">
            <!-- INformación de la reserva -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <div class="mb-5">
                    <label for="estado">Estado</label>
                    <select id="estado" class="p-2" name="estado" required >
                        <option value=""></option>
                        <?php
                            include("actions/obtener_estados.php");
                            while ($estado=mysqli_fetch_array($estados)) {
                                $selected = ($estado['id'] == $plan['estado_id']) ? 'selected' : '';
                                echo "<option value='".$estado['id']."' $selected>".$estado['estado']."</option>";
                            };  
                        ?>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="usuario_venta_id">Usuario Venta</label>
                    <select id="usuario_venta_id" class="p-2 " name="usuario_venta_id" >
                        <option value="null"></option>
                        <?php
                            include("actions/obtener_usuario_asesores.php");
                            while ($usuario=mysqli_fetch_array($usuarios)) {
                                $selected = ($usuario['idusuario'] == $plan['usuario_venta_id']) ? 'selected' : '';
                                echo "<option value='".$usuario['idusuario']."' $selected>".$usuario['nombre']."</option>";
                            };  
                        ?>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="monto_reserva" >Monto Reserva</label>
                    <input type="text" id="monto_reserva" name="monto_reserva"  value="<?php echo $planUuId ? $plan['monto_reserva']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="cliente" >Cliente</label>
                    <input type="text" id="cliente" name="cliente" value="<?php echo $planUuId ? $plan['cliente']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" class="p-2 " name="sexo" >
                        <option value=""></option>
                        <option value="M" <?php echo $planUuId && $plan['sexo'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo $planUuId && $plan['sexo'] == 'F' ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="fecha_nacimiento" >Fecha Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $planUuId ? $plan['fecha_nacimiento']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="edad" >Edad</label>
                    <input type="text" id="edad" name="edad" value="<?php echo $planUuId ? $plan['edad']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="dni" >DNI</label>
                    <input type="text" id="dni" name="dni" value="<?php echo $planUuId ? $plan['dni']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="cuil" >CUIL / CUIT</label>
                    <input type="text" id="cuil" name="cuil" value="<?php echo $planUuId ? $plan['cuil']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="direccion" >Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo $planUuId ? $plan['direccion']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="localidad" >Localidad</label>
                    <input type="text" id="localidad" name="localidad" value="<?php echo $planUuId ? $plan['localidad']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="provincia" >Provincia</label>
                    <input type="text" id="provincia" name="provincia" value="<?php echo $planUuId ? $plan['provincia']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="email" >Email</label>
                    <input type="text" id="email" name="email" value="<?php echo $planUuId ? $plan['email']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>
                <div class="mb-5">
                    <label for="celular" >Celular</label>
                    <input type="text" id="celular" name="celular" value="<?php echo $planUuId ? $plan['celular']:'';  ?>" class="p-2 text-right pr-4" value=""  />
                </div>

            </div>
            <div class="flex justify-end border-t pt-5">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Guardar</button>
            </div>

        </form>

    </div>

</body>
<?php
    mysqli_close($con);
?>
</html>