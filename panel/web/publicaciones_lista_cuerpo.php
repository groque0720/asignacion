<?php
	$SQL="SELECT * FROM publicaciones ORDER BY fecha DESC, id_publicacion DESC LIMIT 200";
	$res=mysqli_query($con, $SQL);
 ?>
<table class="tabla-default">
	<thead>
		<tr>
			<td width="3%">Fecha</td>
			<td width="5%">Sucursal</td>
			<td width="5%">Asesor</td>
			<td width="5%">Tema</td>
			<td width="10%">Observación</td>
			<td width="3%">Ver</td>
			<td width="3%">Vistos</td>


		</tr>
	</thead>
	<tbody>
		<?php
			$nro_fila=0;
			while ($app=mysqli_fetch_array($res)) { ?>
			<tr>
				<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($app['fecha']); ?></div></td>
				<td>
					<?php
						if ($app['idsucursal']==0) {
							$sucursal='Todas';
						}else{
							$SQL="SELECT * FROM sucursales WHERE idsucursal = ".$app['idsucursal'];
							$res_suc = mysqli_query($con, $SQL);
							$suc=mysqli_fetch_array($res_suc);
							$sucursal = $suc['sucursal'];
						}
					 ?>
					<div class="centrar-texto"><?php echo $sucursal; ?></div></td>
				<td>
					<?php
						if ($app['idusuario']==0) {
							$asesor='Todos';
						}else{
							$SQL="SELECT * FROM usuarios WHERE idperfil = 3 AND idusuario = ".$app['idusuario'];
							$res_suc = mysqli_query($con, $SQL);
							$suc=mysqli_fetch_array($res_suc);
							$asesor = $suc['nombre'];
						}
					 ?>
					 <div class="centrar-texto"><?php echo $asesor; ?></div>
				</td>
				<td>
					<?php

						$SQL="SELECT * FROM publicaciones_temas WHERE id_publicacion_tema = ".$app['id_tema'];
						$res_publ = mysqli_query($con, $SQL);
							$suc=mysqli_fetch_array($res_publ);
							$tema = $suc['tema'];

					 ?>
					<div class="centrar-texto"><?php echo $tema; ?>
				</td>
				<td><div class="centrar-texto"><?php echo $app['obs']; ?></td>
				<td><div class="centrar-texto"><a class="icon-buscar espacio"  target="_blank" href="<?php echo $app['url']; ?>">Ver</a><!-- ||<a class="icon-menu espacio"  target="_blank" href="<?php echo $app['url']; ?>">Editar</a> --></div></td>
				<td>
					<?php

						//$SQL="SELECT count(visto) as cantidad FROM publicaciones_linea WHERE id_publicacion =" .$app['id_publicacion'];
						//$res_cant = mysqli_fetch_array(mysql_query($SQL));


						//$SQL="SELECT count(visto) as cantidad FROM publicaciones_linea WHERE visto = 1 AND id_publicacion =" .$app['id_publicacion'];
						//$res_visto = mysqli_fetch_array(mysql_query($SQL));


						//$SQL="SELECT count(visto) as cantidad FROM publicaciones_linea WHERE visto = 0 AND id_publicacion =" .$app['id_publicacion'];
						//$res_no_visto = mysqli_fetch_array(mysql_query($SQL));


					 ?>
					 <div class="centrar-texto">
					 	<a href="#"><span class="icon-arriba estacio texto-verde"><?php //echo " ".$res_visto['cantidad'];?></span></a>
					 	<span>||</span>
					 	<a href="#"><span class="icon-abajo texto-rojo"><?php //echo " ".$res_no_visto['cantidad']; ?></span></a>
					 </div>
				</td>
			</tr>
		<?php } ?>

	</tbody>
</table>