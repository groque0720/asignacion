<?php 

include('../z_comun/vista.php');

extract($_GET);

$id_evaluacion_usuario = $id;

?>

<link rel="stylesheet" href="css/estilo-evaluacion.css">

<div class="zona_tabla ancho-100">

	<table class="eval-tabla ancho-90">
		<colgroup>
			<col width="90%">
			<col width="10%">
		</colgroup>
		<thead>
			<tr>
				<td>FACTORES</td>
				<td>CALIFICACIÓN</td>
			</tr>
		</thead>
		<tbody>

			<?php 

			$SQL="SELECT * FROM evaluacion_item ORDER BY posicion";
			$factores = mysqli_query($con, $SQL);
			$nro=0;

			while ($factor = mysqli_fetch_array($factores)) { $nro++;?>
				<tr>
					<td>
						<div class="eval-titulo">
							<?php echo $nro.' - '.$factor['titulo']; ?>
						</div>
						<div class="eval-descripcion">
							<?php echo $factor['descripcion']; ?>
						</div>
						<div class="eval-obs">
							<?php echo $factor['observacion'] ?>
						</div>
					</td>
					<td class="centrar-texto">
						<div class="<?php echo 'calificacion item-'.$factor['id_item']; ?>">


							<?php 

								$SQL="SELECT * FROM evaluacion_usuario_calificacion WHERE id_item = ".$factor['id_item']." AND id_evaluacion_usuario =".$id_evaluacion_usuario;
								$calificaciones = mysqli_query($con, $SQL);
								$calificacion = mysqli_fetch_array($calificaciones);

								if ($e=='auto') {
									$valor= $calificacion['calificacion_autoevaluacion'];

								}else{
									$valor= $calificacion['calificacion_superior'];
								}
							 ?>

							<?php 
							if ($valor==0) {?>
								<a href="" class="<?php echo 'abrir-calificador nroitem-'.$factor['id_item']; ?> " data-idevaluacion="<?php echo $calificacion['id_evaluacion_usuario_calificacion']; ?>" data-iditem="<?php echo $factor['id_item']; ?>" ><i class="material-icons">exposure</i></a>
							<?php }else { ?>
								<a class="<?php echo 'abrir-calificador nroitem-'.$factor['id_item']; ?>" data-idevaluacion="<?php echo $calificacion['id_evaluacion_usuario_calificacion']; ?>" data-iditem="<?php echo $factor['id_item']; ?>"><span class="calificacion-valor"><?php echo $valor; ?></span></a>
							<?php } ?>

						</div>

					</td>
				</tr>							
			<?php } ?>
		</tbody>
	</table>
</div>
<br>

<?php 
	$SQL="SELECT * FROM evaluaciones_usuarios WHERE id_evaluacion_usuario =".$id_evaluacion_usuario;
	$res=mysqli_query($con, $SQL);
	$reg_terminado=mysqli_fetch_array($res);
	$no_terminado="<center><input type='button' style='color: red;' class='boton-terminar' data-id='$id_evaluacion_usuario' value='Terminar'></center>";

?>

<div class="flexible">
	<div class="zona-boton-terminar" style="width: 30%;" ><center><input type="button" class="boton-volver" value="<-- Volver">	</center></div>
	<div class="zona-boton-terminar" style="width: 30%;">
		<?php 
			if (($e=="auto" AND  $reg_terminado['terminado_autoevaluacion'] == 0) OR ($e=="supauto" AND $reg_terminado['terminado_superior'] == 0)) { ?>
			<?php echo $no_terminado; ?>
		<?php 	} ?>
			
	</div>
	

</div>

<input type="hidden" id="tipo_evaluacion" value="<?php echo $e; ?>">
<input type="hidden" id='terminado_autoevaluacion' value="<?php echo $reg_terminado['terminado_autoevaluacion']; ?>">
<input type="hidden" id='terminado_superior' value="<?php echo $reg_terminado['terminado_superior']; ?>">

<script>
	
$(".abrir-calificador").click(function(event){
	event.preventDefault();

	id_item=$(this).attr('data-iditem');
	id_evaluacion = $(this).attr('data-idevaluacion');
	tipo_evaluacion = $("#tipo_evaluacion").val();

	if (tipo_evaluacion=='auto' || tipo_evaluacion=='supauto') {

		$.ajax({
			url:"buscar_item.php",
			cache:false,
			type:"POST",
			data:{id_item, id_evaluacion,tipo_evaluacion},
			success:function(result){

	 			$(".zona-calificacion").html(result);
				$(".lienzo-calificacion").show(); 
		    }
		});
	}

});

$(".boton-terminar").click(function(){

	id_evaluacion_usuario=$(this).attr('data-id');
	tipo_evaluacion = $("#tipo_evaluacion").val();

	$.ajax({
		url:"cerrar_evaluacion.php",
		cache:false,
		type:"POST",
		data:{id_evaluacion_usuario,tipo_evaluacion},
		success:function(result){
			swal('Muchas Gracias','','success').then(function(){window.history.back(); });
	    }
	});


});
$(".boton-volver").click(function(){
	window.history.back();
});
</script>   