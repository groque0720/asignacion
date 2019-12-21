
<?php 

	$SQL="SELECT * FROM usuarios WHERE idperfil = 3 AND activo = 1 AND id_negocio = 1 ORDER BY idsucursal, posicion";
	$asesores=mysqli_query($con, $SQL);

	while ($asesor = mysqli_fetch_array($asesores)) { 

		$SQL="SELECT * FROM ect_asesores WHERE id_asesor = ".$asesor["idusuario"];
		$asesores_activos= mysqli_query($con, $SQL);

		$cant=mysqli_num_rows($asesores_activos);

		if ($cant==0) {
			$SQL="INSERT INTO ect_asesores (id_asesor) VALUES (  {$asesor["idusuario"]} )";
			mysqli_query($con, $SQL);
		}

 } ?>

<table class="listado_gestoria ancho-60">
	<colgroup>
			<col width="7%"> 
			<col width="7%">
			<col width="5%">
			<col width="2%">
	</colgroup>
	<thead>
		<tr>
			<td>Sucursal</td>
			<td>Asesor</td>
			<td>Activo</td>
			<td><span class="icon-delete"></span></td>
		</tr>
	</thead>
	<tbody class="lista-unidades">
		<?php 

			$SQL="SELECT * FROM sucursales";
			$sucursales=mysqli_query($con, $SQL);
			$sucursal_a[0]['sucursal']= '-';
			$i=1;
			while ($sucursal=mysqli_fetch_array($sucursales)) {
				$sucursal_a[$i]['sucursal']= $sucursal['sucursal'];
				$i++;
			}

			$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
			$usuarios=mysqli_query($con, $SQL);
			$usuario_a[1]['nombre']= '-';
			$i=1;
			while ($usuario=mysqli_fetch_array($usuarios)) {
				$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
				$i++;
			}

			$SQL="SELECT usuarios.idsucursal AS id_sucursal, ect_asesores.id AS id, ect_asesores.id_asesor AS id_asesor, ect_asesores.objetivo_activo AS activo
					FROM ect_asesores INNER JOIN usuarios ON ect_asesores.id_asesor = usuarios.idusuario WHERE ect_asesores.borrar = 0 ORDER BY id_sucursal ASC, usuarios.posicion";
			$asesores=mysqli_query($con, $SQL);
		$fila=0;
		while ($asesor = mysqli_fetch_array($asesores)) { $fila++;?>

		<tr class="<?php echo 'fila_'.$fila; ?>">
			<td class="centrar-texto" ><?php echo $sucursal_a[$asesor["id_sucursal"]]['sucursal']; ?></td>
			<td class="centrar-texto" ><?php echo $usuario_a[$asesor['id_asesor']]['nombre']; ?></td>
			<td class="centrar-texto" >
				<?php
					if ($asesor['activo']==1) {
						$check_activo='checked';
					}else{
						$check_activo='';
					}
				?>
			<input class="check_activo" type="checkbox" data-id="<?php echo $asesor['id']; ?>" <?php echo $check_activo; ?>>
			</td>
			<td class="centrar-texto" ><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $asesor['id']; ?>" class="icon-delete borrar-activacion"></span></td>

		</tr>

		 <?php } ?>

	</tbody>
	</table>
<script src="js/asesores.js"></script>