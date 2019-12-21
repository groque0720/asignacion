
<?php 

$SQL="SELECT * FROM ect_tipos_objetivos WHERE activo = 1";
$objetivos=mysqli_query($con, $SQL);
$cant_objetivos = mysqli_num_rows($objetivos);

if ($cant_objetivos>1) {
	$i=0;
	while ($objetivo=mysqli_fetch_array($objetivos)) {
		$objetivos_array[$i]["id"]=$objetivo["id"];
		$objetivos_array[$i]["tipo_objetivo"]=$objetivo["tipo_objetivo"];
		$i++;
	}

}

// for ($i=0; $i < $cant_objetivos; $i++) { 
// 	echo $objetivos_array[$i]["tipo_objetivo"]." - - ";
// }

 ?>
<table class="listado_gestoria ancho-70">
	<colgroup>
			<col width="7%"> 
			<col width="7%">
			<?php 
			for ($i=0;  $i < $cant_objetivos; $i++) { ?>
				<col width="3.5%">
			<?php } ?>
	</colgroup>
	<thead>
		<tr>
			<td>Sucursal</td>
			<td>Asesor</td>
			<?php 
			for ($i=0; $i < $cant_objetivos; $i++) { ?>
				<td><?php echo  $objetivos_array[$i]["tipo_objetivo"];?></td>
			<?php } ?>	

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

			$SQL="SELECT * FROM ect_view_asesores_activos";
			$asesores=mysqli_query($con, $SQL);


			while ($asesor = mysqli_fetch_array($asesores)) {?>

		<tr class="<?php echo 'fila_'.$fila; ?>">
			<td class="centrar-texto" ><?php echo $sucursal_a[$asesor["id_sucursal"]]['sucursal']; ?></td>
			<td class="centrar-texto" ><?php echo $asesor["asesor"]; ?></td>
			<?php 
				for ($i=0; $i < $cant_objetivos; $i++) { 

					$SQL="SELECT * FROM ect_asesores_r_objetivos WHERE id_asesor_ect = ".$asesor['id_asesor_ect']." AND id_tipo_objetivo = ".$objetivos_array[$i]["id"];

					$objetivo_activos=mysqli_query($con, $SQL);

					$objetivo_activo = mysqli_fetch_array($objetivo_activos);

					if ($objetivo_activo['activo']==1) {
						$check_activo='checked';
					}else{
						$check_activo='';
					}

				 ?>

				<td class="centrar-texto"><input class="check_activo" data-id="<?php echo $objetivo_activo['id']; ?>" type="checkbox" <?php echo $check_activo; ?>></td>

			<?php } ?>
		</tr>
		 <?php } ?>

	</tbody>
	</table>
<script src="js/asesores_objetivos.js"></script>