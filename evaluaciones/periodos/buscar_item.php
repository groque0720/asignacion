<?php 

	include("../z_comun/funciones/funciones.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

?>



<div class="<?php echo 'zona-clasificacion zona-'.$id_item; ?>" data-item="<?php echo $id_item; ?>">
	<?php 

		$SQL="SELECT * FROM evaluacion_item WHERE id_item = ".$id_item;
		$items = mysqli_query($con, $SQL);
		$item = mysqli_fetch_array($items);

		?>

			<div class="item-descripcion-lienzo">
				<span class="titulo-item"><?php echo $item['descripcion']; ?></span>
			</div>

		<?php 

		$SQL="SELECT * FROM evaluacion_calificacion WHERE id_item = ".$id_item;
		$calificaciones = mysqli_query($con, $SQL);


		$SQL="SELECT * FROM evaluacion_usuario_calificacion WHERE id_evaluacion_usuario_calificacion = ".$id_evaluacion;
		$evals=mysqli_query($con, $SQL);
		$eval=mysqli_fetch_array($evals);


		$i=0;

		echo '<br>';

		while ($calificacion=mysqli_fetch_array($calificaciones)) { $i++;

			if ($tipo_evaluacion=='auto') {

				if ($eval['calificacion_autoevaluacion']==$calificacion['calificacion']) {
					$valor_class="item-calificacion item-activo";
				}else{
					$valor_class="item-calificacion";
				}
				
			}else{

				if ($eval['calificacion_superior']==$calificacion['calificacion']) {
					$valor_class="item-calificacion item-activo";
				}else{
					$valor_class="item-calificacion";
				}
			}

			?>
			<a href=""  class="<?php echo 'item item-'.$i; ?> " data-idevaluacion="<?php echo $eval['id_evaluacion_usuario_calificacion'];  ?>" data-valor="<?php echo $calificacion['calificacion']; ?>">
				<div class="<?php echo $valor_class.' div_item_'.$calificacion['calificacion']; ?>" style="font-size: 14px;"> <?php echo $calificacion['detalle']; ?></div>
			</a>
	<?php } ?>									

</div>
<br>
<input type="hidden" id="tipo_evaluacion" value="<?php echo $tipo_evaluacion; ?>">

<div class="observacion">
	<label for="observacion" class="item-descripcion-lienzo">Observación:</label>
	<textarea style="width: 100%; font-size: 14px;" data-idevaluacion="<?php echo $eval['id_evaluacion_usuario_calificacion'];  ?>" name="observacion" id="observacion_item" cols="30" rows="5"><?php if ($tipo_evaluacion=='supauto') {
			echo $eval['observacion_superior'];
		}else{
			echo $eval['observacion_auto'];
		} ?></textarea>
</div>
<div style="text-align: right;">
	<button class="cerrar-lienzo">Guardar/Cerrar</button>
</div>

<script src="js/buscar_item.js"></script>  