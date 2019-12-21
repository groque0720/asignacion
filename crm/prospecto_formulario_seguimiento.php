<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
?>

<table class="listado_gestoria">
	<colgroup>
			<col width="2%">
			<col width="2%">
			<col width="1%">
			<col width="2%">
			<col width="2%">
	</colgroup>
	<thead>
		<tr>
			<td>Prox. Cto.</td>
			<td>Hora</td>
			<td>Realizado</td>
			<td>Fecha</td>
			<td>Resultado</td>
		</tr>
	</thead>
	<tbody class="lista-unidades">
		<?php

			$SQL="SELECT * FROM prospectos_seguimiento_resultados";
			$resultados=mysqli_query($con, $SQL);
			$resultado_a[0]['resultado']= '-';
			while ($resultado=mysqli_fetch_array($resultados)) {
				$resultado_a[$resultado['id']]['resultado']= $resultado['resultado'];
			}
			$hay_abiertos = 0;
			$SQL="SELECT * FROM prospectos_seguimientos WHERE id_prospecto =".$prospecto['id']." ORDER BY fec_contacto ASC" ;
			$seguimientos = mysqli_query($con, $SQL);
			while ($seguimiento = mysqli_fetch_array($seguimientos)) { ?>
				<tr class="fila-seguimiento" data-id="<?php echo $seguimiento['id']; ?>">
					<td class="centrar-texto"><?php echo cambiarFormatoFecha($seguimiento['fec_contacto']); ?></td>
					<td class="centrar-texto"><?php echo cambiarFormatohora($seguimiento['hora']); ?></td>
					<td class="centrar-texto"><?php if ($seguimiento['realizado']==1) { echo 'Si';}else{ echo 'No'; $hay_abiertos++; }?></td>
					<td class="centrar-texto"><?php if ($seguimiento['realizado']==1) { echo cambiarFormatoFecha($seguimiento['fec_realizado']); }else{echo '-';} ?></td>
					<td class="centrar-texto"><?php if ($seguimiento['realizado']==1) { echo $resultado_a[$seguimiento['id_resultado']]['resultado']; }else{echo '-';} ?></td>
				</tr>
		<?php } ?>
		
	</tbody>
</table>

<input type="hidden" id="hay_abiertos" value="<?php echo $hay_abiertos; ?>">

<script src="js/prospecto_formulario_seguimiento.js"></script>
