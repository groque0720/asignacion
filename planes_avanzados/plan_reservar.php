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

    if($plan['estado_id'] != 1 AND $plan['usuario_venta_id'] != $userId) {
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
    <!-- Prevent automatic refresh -->
    <meta http-equiv="expires" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
</head>
<body>
    <div class="container m-auto ">
        <?php
            $titulo = "Reserva Plan Avanzado";
            include("components/cabecera.php");
        ?>

        <form id="reserva-form" class=" mx-auto p-5 border rounded" autocomplete="off" action="actions/reservar_plan_avanzado.php" method="POST" >

        <input type="text" id="planUuId" class="p-2 text-right pr-4" name="planUuId" value="<?php echo $planUuId ? $plan['uuid']:'';  ?>" hidden  />
        <input type="date" id="planUuId" class="p-2 text-right pr-4" name="fecha_reserva" value="<?php echo date("Y-m-d");  ?>"  hidden  />
        <input type="time" id="planUuId" class="p-2 text-right pr-4" name="hora_reserva" value="<?php echo date("H:i");  ?>"  hidden  />
        <!-- <input type="text" id="planUuId" class="p-2 text-right pr-4" name="planUuId" value="" hidden  /> -->

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-5">

                <div class="">
                    <label for="modelo">Modelo - Versión</label>
                    <div class="input_info"><?php echo $plan['modelo'] ?></div>
                </div>


                <div class="">
                    <label for="modalidad">Modalidad - Plazo</label>
                    <div class="input_info"><?php echo $plan['modalidad'] ?></div>
                </div>
                
                <div class="">
                    <label for="grupo_orden" >Grupo - Orden</label>
                    <div class="input_info text-right"><?php echo $plan['grupo_orden'] ?></div>
                </div>

                <div class="">
                    <label for="cuotas_pagadas_cantidad" >Cantidad Cuotas Pagadas </label>
                    <div class="input_info text-right"><?php echo $plan['cuotas_pagadas_cantidad'] ?></div>
                </div>

                <div class="">
                    <label for="cuota_promedio" >Cuota Promedio</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['cuota_promedio'], 2, ',', '.'); ?></div>
                </div>

                <div class="">
                    <label for="valor_unidad" >Valor Unidad</label>
                    <div class="input_info text-right"><?php echo '$ '.number_format($plan['valor_unidad'], 2, ',', '.'); ?></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-5">

                <div class="">
                    <label for="venta" class="text-orange-500">Precio Venta</label>
                    <input type="text" id="venta"  name="venta"  class="p-2 text-right pr-4" value="<?php echo $planUuId ? $plan['venta']:'';  ?>" required  />
                </div>

                <div class="">
                    <label for="integracion" class="text-orange-500" >Integración</label>
                    <input type="text" id="integracion" name="integracion" required value="<?php echo $planUuId ? $plan['integracion']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>

                <div class="">
                    <label for="derecho_adjudicacion" class="text-orange-500" >Derecho de Adjudicación</label>
                    <input type="text" id="derecho_adjudicacion" name="derecho_adjudicacion" required value="<?php echo $planUuId ? $plan['derecho_adjudicacion']:'';  ?>" class="p-2 text-right pr-4"   />
                </div>

                <div class="">
                    <label for="precio_final" >Precio Final <span class="text-orange-500">(Venta+Integracion + Adjud.)</span></label>
                    <input type="text" id="precio_final"  name="precio_final"  class="p-2 text-right pr-4 text-red-600 font-bold" value="<?php echo $planUuId ? $plan['precio_final']:'';  ?>" required  />
                </div>
                
            </div>
            <div class=" w-full mb-5">
                <label for="observaciones" >Observación</label>
                <textarea class="p-2" id="observaciones" name="observaciones" rows="5"><?php echo $planUuId  ? $plan['observaciones'] : '';  ?></textarea>
                <!-- <input type="text" id="observacion" name="observacion"  value="<?php echo $plan['observacion'];  ?>" class="p-2 text-right pr-4"  /> -->
            </div>
            <hr class="mb-5">
            <!-- INformación de la reserva -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-5">
                <div class="">
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
                <div class="">
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
                <div class="">
                    <label for="monto_reserva" class="text-red-600" >Monto Reserva <sup>(*)</label>
                    <input type="text" id="monto_reserva" name="monto_reserva" required  value="<?php echo $planUuId ? $plan['monto_reserva']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="modelo_version_retirar" class="text-red-600" >Modelo Versión Final <sup>(*)</label>
                    <input type="text" id="modelo_version_retirar" name="modelo_version_retirar" required  value="<?php echo $plan['modelo_version_retirar'];  ?>" class="p-2 text-right pr-4"  />
                </div>
 
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-5">
            <div class="">
                    <label for="cliente " class="text-red-600" >Cliente <sup>(*)</sup></label>
                    <input type="text" id="cliente" name="cliente" required value="<?php echo $planUuId ? $plan['cliente']:'';  ?>" class="p-2 text-right pr-4"   />
                </div>
                <div class="">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" class="p-2 " name="sexo" required>
                        <option value=""></option>
                        <option value="M" <?php echo $planUuId && $plan['sexo'] == 'M' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="F" <?php echo $planUuId && $plan['sexo'] == 'F' ? 'selected' : ''; ?>>Femenino</option>
                    </select>
                </div>
                <div class="">
                    <label for="fecha_nacimiento" >Fecha Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required value="<?php echo $planUuId ? $plan['fecha_nacimiento']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="edad" >Edad</label>
                    <input type="text" id="edad" name="edad" required value="<?php echo $planUuId ? $plan['edad']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="dni" >DNI</label>
                    <input type="text" id="dni" name="dni" required value="<?php echo $planUuId ? $plan['dni']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="cuil" >CUIL / CUIT</label>
                    <input type="text" id="cuil" name="cuil" required value="<?php echo $planUuId ? $plan['cuil']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="direccion" >Dirección</label>
                    <input type="text" id="direccion" name="direccion" required value="<?php echo $planUuId ? $plan['direccion']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="localidad" >Localidad</label>
                    <input type="text" id="localidad" name="localidad" required value="<?php echo $planUuId ? $plan['localidad']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="provincia" >Provincia</label>
                    <input type="text" id="provincia" name="provincia" required value="<?php echo $planUuId ? $plan['provincia']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="email" >Email</label>
                    <input type="text" id="email" name="email" required value="<?php echo $planUuId ? $plan['email']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>
                <div class="">
                    <label for="celular" >Celular</label>
                    <input type="text" id="celular" name="celular" required value="<?php echo $planUuId ? $plan['celular']:'';  ?>" class="p-2 text-right pr-4"  />
                </div>

            </div>
            <div class="flex justify-between border-t pt-5">
                <div class="text-gray-500 text-sm italic" id="auto-save-status"></div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Reservar</button>
            </div>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reserva-form');
            const formId = '<?php echo $planUuId ? $plan["uuid"] : "new"; ?>';
            const saveStatus = document.getElementById('auto-save-status');
            
            // Function to save all form data
            function saveFormData() {
                const formDataObj = {};
                
                // Get all input, select, and textarea elements
                const inputs = form.querySelectorAll('input, select, textarea');
                
                inputs.forEach(input => {
                    // Skip hidden inputs and disabled fields
                    if (input.type !== 'hidden' && !input.disabled) {
                        formDataObj[input.name] = input.value;
                    }
                });
                
                // Save to localStorage if there's data
                if (Object.keys(formDataObj).length > 0) {
                    try {
                        localStorage.setItem('reserva_form_' + formId, JSON.stringify(formDataObj));
                        
                        // Update save status message
                        const now = new Date();
                        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                                       now.getMinutes().toString().padStart(2, '0') + ':' + 
                                       now.getSeconds().toString().padStart(2, '0');
                        saveStatus.textContent = 'Datos guardados automáticamente a las ' + timeStr;
                        
                    } catch (e) {
                        console.error("Error saving form data:", e);
                    }
                }
            }
            
            // Restore form data on page load
            function restoreFormData() {
                const savedData = localStorage.getItem('reserva_form_' + formId);
                if (savedData) {
                    try {
                        const formDataObj = JSON.parse(savedData);
                        
                        // Populate form fields
                        for (const name in formDataObj) {
                            const input = form.elements[name];
                            if (input && !input.disabled) {
                                input.value = formDataObj[name];
                            }
                        }
                        
                        saveStatus.textContent = 'Datos restaurados de guardado anterior';
                    } catch (e) {
                        console.error('Error restoring data:', e);
                    }
                }
            }
            
            // Automatically save data every 5 seconds
            const autosaveInterval = setInterval(saveFormData, 5000);
            
            // Save on input changes (debounced)
            let debounceTimer;
            form.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(saveFormData, 500);
            });
            
            // Also save on change events (for select boxes)
            form.addEventListener('change', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(saveFormData, 500);
            });
            
            // Restore data on page load
            restoreFormData();
            
            // Clear saved data when form is successfully submitted
            form.addEventListener('submit', function() {
                localStorage.removeItem('reserva_form_' + formId);
            });
            
            // Show warning when navigating away
            window.addEventListener('beforeunload', function(e) {
                // Save data before potentially leaving
                saveFormData();
                
                const formData = new FormData(form);
                let formHasData = false;
                
                for (const [name, value] of formData.entries()) {
                    const input = form.elements[name];
                    if (input && !input.hasAttribute('hidden') && !input.disabled && value) {
                        formHasData = true;
                        break;
                    }
                }
                
                if (formHasData) {
                    e.preventDefault();
                    e.returnValue = '¿Estás seguro de abandonar la página? Los datos no se perderán y estarán disponibles cuando vuelvas.';
                    return e.returnValue;
                }
            });
            
            // Override default browser page session timeout
            // This is just for additional protection, the iframe is the main solution
            function resetSessionTimeout() {
                const img = new Image();
                img.src = 'session_ping.php?r=' + Math.random();
            }
            setInterval(resetSessionTimeout, 60000); // Every minute
        });
    </script>
</body>
<?php
    mysqli_close($con);
?>
</html>