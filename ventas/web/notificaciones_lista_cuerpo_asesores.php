<?php
	$SQL="SELECT * FROM view_publicaciones WHERE id_tema=".$_GET['id_tema']." AND idusuario = ".$_GET['id']." ORDER BY fecha DESC, id_publicacion DESC";
	$res=mysqli_query($con, $SQL);
 ?>
<table class="tabla-default">
	<thead>
		<tr>
			<td width="3%">Fecha</td>
			<td width="5%">Tema</td>
			<td width="10%">Observación</td>
			<td width="3%">Ver</td>
			<td width="3%">Estado</td>


		</tr>
	</thead>
	<tbody>
		<?php
			$nro_fila=0;
			while ($app=mysqli_fetch_array($res)) { $nro_fila=$nro_fila + 1;?>
			<tr>
				<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($app['fecha']); ?></div></td>

				<td>
					<?php

						$SQL="SELECT * FROM publicaciones_temas WHERE id_publicacion_tema = ".$_GET['id_tema'];
						$res_publ = mysqli_query($con, $SQL);
							$suc=mysqli_fetch_array($res_publ);
							$tema = $suc['tema'];

					 ?>
					<div class="centrar-texto"><?php echo $tema; ?>
				</td>
				<td><div class="centrar-texto"><?php echo $app['obs']; ?></td>
				<td><div class="centrar-texto"><a id="click_ver" class ="<?php echo 'click_ver  click_visto_'.$app['id_publicacion_linea']; ?>" data-id="<?php echo $app['id_publicacion_linea']; ?>" class="icon-buscar espacio"  target="_blank" href="<?php echo $app['url']; ?>">Ver</a><!-- ||<a class="icon-menu espacio"  target="_blank" href="<?php echo $app['url']; ?>">Editar</a> --></div></td>
				<td>
					<div id="sivisto" class="<?php echo 'centrar-texto ojo_'.$app['id_publicacion_linea']; ?>">
						<?php
						 if ($app['visto']==1) { ?>
							<span class="icon-aceptar espacio visto">Visto</span>
						<?php }else{ ?>
							<span class="icon-cerrar espaccio novisto">No Visto</span>
						<?php } ?>
					</div>
				</td>
			</tr>
		<?php } ?>

	</tbody>
</table>

<script>
	$(".click_ver").click(function(){

		var id = $(this).attr('data-id');
		$(".ojo_"+id).html('<span class="icon-aceptar espacio visto">Visto</span>');

		$.ajax({
			url:"notificaciones_procesar_visto.php",
			cache:false,
			type:"POST",
			data:{id:id},
			success:function(result){
			}
		})
	})
</script>