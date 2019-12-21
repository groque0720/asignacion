
<?php 

	$SQL="SELECT * FROM ect_tipos_objetivos WHERE borrar = 0 ORDER BY posicion";
	$objetivos=mysqli_query($con, $SQL);

?>

<table class="ancho-50">
	<colgroup>
			<col width="7%"> 
			<col width="5%">
			<col width="2%">
	</colgroup>
	<thead>
		<tr>
			<td>Objetivos</td>
			<td>Activo</td>
			<td><span class="icon-delete"></span></td>
		</tr>
	</thead>
	<tbody class="">
	<?php 
		$fila=0;
		while ($objetivo = mysqli_fetch_array($objetivos)) { $fila++;?>

		<tr class="<?php echo 'fila_'.$fila; ?>">
			<td class="centrar-texto" ><?php echo $objetivo['tipo_objetivo']; ?></td>
			<td class="centrar-texto" >
				<?php
					if ($objetivo['activo']==1) {
						$check_activo='checked';
					}else{
						$check_activo='';
					}
				?>
			<input class="check_activo" type="checkbox" data-id="<?php echo $objetivo['id']; ?>" <?php echo $check_activo; ?>>
			</td>
			<td class="centrar-texto" ><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $objetivo['id']; ?>" class="icon-delete borrar_tipo_objetivo"></span></td>

		</tr>

		 <?php } ?>

	</tbody>
	</table>
<script src="js/objetivos.js"></script>