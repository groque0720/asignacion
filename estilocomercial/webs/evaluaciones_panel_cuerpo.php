<?php
	$SQL="SELECT * FROM evaluaciones ORDER BY id_evaluacion DESC";
	$res=mysqli_query($con, $SQL);
 ?>
<table class="tabla-default">
	<thead>
		<tr>
			<td width="5%">Fecha</td>
			<td width="5%">Periodo</td>
			<td width="3%">Opción</td>
		</tr>
	</thead>
	<tbody>
		<?php
			$nro_fila=0;
			while ($app=mysqli_fetch_array($res)) { ?>
			<tr>
				<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($app['fecha']); ?></div></td>
				<td><div class="centrar-texto"><?php echo $app['periodo']; ?></div></td>
				<td><a class="icon-menu espacio editar" href="<?php echo 'evaluaciones_lista.php?id='.$app['id_evaluacion']."&per=".$app['periodo']; ?> ">Ver</a></td>
			</tr>
		<?php } ?>

	</tbody>
</table>