<?php 
while ($reg=mysqli_fetch_array($res_reg)) { ?>
	<tr>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['nro_leg']; ?></a></div></td>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['sucursal']; ?></a></div></td>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo cambiarformatofecha($reg['fec_rec_tra']); ?></a></div></td>
		<td><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['nombre']; ?></a></td>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php if ($reg['compra']==1) { echo 'Nuevo';	}else{echo 'Usado';} ?></a></div></td>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['interno']; ?></a></div></td>
		<td><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['modelo'].' '.$reg['version'].' '.$reg['usado']; ?></a></td>
		
		<td><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['asesor']; ?></a></td>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['localidad']; ?></a></div></td>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['patente']; ?></a></div></td>
		<?php if ($reg['estado_cli']=='1') { $class_estaddo_cli='icon-listo completo';}else{$class_estaddo_cli='icon-no-listo incompleto';} ?>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><span class="<?php echo $class_estaddo_cli; ?>"></span></div></td>
		<?php if ($reg['estado_reg']=='1') { $class_estaddo_cli='icon-listo completo';}else{$class_estaddo_cli='icon-no-listo incompleto';} ?>
		<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><span class="<?php echo $class_estaddo_cli; ?>"></span></div></td>
	</tr>
<?php }  ?>