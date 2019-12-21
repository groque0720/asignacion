
<?php 
	// $SQL="SELECT * FROM grupos WHERE activo = 1 AND posicion > 0 ORDER BY posicion";
	// $modelos=mysqli_query($con, $SQL);

	// while ($modelo = mysqli_fetch_array($modelos)) { 

	// 	$SQL="SELECT * FROM ect_modelos WHERE id_modelo = ".$modelo["idgrupo"];
	// 	$modelos_activos= mysqli_query($con, $SQL);

	// 	$cant=mysqli_num_rows($modelos_activos);

	// 	if ($cant==0) {
	// 		$SQL="INSERT INTO ect_modelos (id_modelo, posicion) VALUES (  {$modelo["idgrupo"]} , {$modelo["posicion"]} )";
	// 		mysqli_query($con, $SQL);
	// 	}
 // 	} 
 ?> 
<!-- <div class="centrar ancho-50 margen-abajo-10">
	<a href="" class="nuevo_modelo"><span class="icon-plus"> </span> Agregar Modelo</a>
</div> -->

<table class="ancho-50">
	<colgroup>
			<col width="2%">
			<col width="7%">
			<col width="5%">
			<!-- <col width="5%"> -->
	</colgroup>
	<thead>
		<tr>
			<td>Posición</td>
			<td>Modelos</td>
			<td>Activo</td>
<!-- 			<td>
				<div class="ancho-80 flexible centrar-caja">
					<span class="icon-cambio flexible-auto"></span>
					<span class="icon-chevron-up flexible-auto"></span>
					<span class="icon-chevron-down flexible-auto"></span>
					<span class="icon-delete flexible-auto"></span></td>
				</div>
			</tr> -->
	</thead>
	<?php 

		$SQL="SELECT * FROM grupos WHERE activo = 1 ";
		$grupos=mysqli_query($con, $SQL);
		$grupo_a[]['grupo']= '-';
		$i=1;
		while ($grupo=mysqli_fetch_array($grupos)) {
			$grupo_a[$grupo['idgrupo']]['grupo']= $grupo['grupo'];
			$grupo_a[$grupo['idgrupo']]['agrupar']= $grupo['agrupar'];
			$grupo_a[$grupo['idgrupo']]['nombre_agrupacion']= $grupo['nombre_agrupacion'];
			$i++;
		}

	 ?>

	<tbody class="tabla_cuerpo_modelos">
		<?php 

		$SQL="SELECT * FROM ect_modelos WHERE borrar = 0 ORDER BY posicion";
		$modelos= mysqli_query($con, $SQL);

		$fila=0;
		while ($modelo = mysqli_fetch_array($modelos)) { $fila++;?>

		<tr class="<?php echo 'fila_'.$fila; ?>">
			<td class="centrar-texto" ><?php echo $modelo['posicion']; ?></td>
			<td class="centrar-texto" ><?php echo $modelo['modelo']; ?></td>
			<td class="centrar-texto" >
				<?php
					if ($modelo['activo']==1) {
						$check_activo='checked';
					}else{
						$check_activo='';
					}
				?>
			<input class="check_activo" type="checkbox" data-id="<?php echo $modelo['id']; ?>" <?php echo $check_activo; ?>>
			</td>
			<!-- <td class="centrar-texto" >
			<span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $modelo['id']; ?>" class="icon-delete borrar-modelo"></span>
			<span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $modelo['id']; ?>" class="icon-delete borrar-modelo"></span>
			<span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $modelo['id']; ?>" class="icon-delete borrar-modelo"></span>
			<span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $modelo['id']; ?>" class="icon-delete borrar-modelo"></span>
			</td> -->

		</tr>

		 <?php } ?>

	</tbody>
	</table>
<script src="js/modelos.js"></script>