
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

 ?>

 <input type="hidden" id="mes" value="<?php echo $mes; ?>">
 <input type="hidden" id="ano" value="<?php echo $ano; ?>">
 <input type="hidden" id="id_asesor" value="<?php echo $id_asesor; ?>">

<div class="ancho-80 centrar-caja flexible" style="background: white; margin: auto; border-radius: 10px; ">

	<div class="cuadro" style="margin: 5px; border-radius: 10px; overflow: auto;border-color: #DDDDDD;">
	
		<div class="flexible">
			<div class="ancho-95 centrar-texto negrita margen-arriba-5"> Lista de Item - Plan de Acción Mensual </div>
			<div class="ancho-5 derecha-texto" ><span class="icon-times cerrar-lista-check" style="font-size: 20px; cursor: pointer;"></span></div>
		</div>
		<hr>

		<table class="margen-arriba-10 ancho-95">
			<col width="5%">
			<col width="45%">
			<col width="45%">
			<thead>
				<tr>
					<td>Check</td>
					<td>Detalle</td>
					<td>Observaciones</td>
				</tr>
			</thead>
			<tbody>
			<?php 
				$SQL="SELECT * FROM ect_pam_detalle WHERE activo = 1 ORDER BY detalle";
				$pmis=mysqli_query($con, $SQL);

				while ($pmi = mysqli_fetch_array($pmis)) { 

					$cant=0;

					$SQL="SELECT * FROM ect_pmi_asesores_pam WHERE id_asesor_ect = $id_asesor AND id_mes = $mes AND ano = $ano AND id_item =".$pmi['id'];
					$items=mysqli_query($con, $SQL);


					$cant = mysqli_num_rows($items);

					if ($cant>=1) {
						$chequeado='checked';
						$deshabilitado ='';
						$item = mysqli_fetch_array($items);
						$obs= $item['observacion'];
					}else{
						$chequeado='';
						$deshabilitado ='disabled';
						$obs= '';
					}

				?>
					<tr>
						<td class="centrar-texto"><input type="checkbox" class="<?php echo 'item item_'.$pmi['id']; ?>"  data-id="<?php echo $pmi['id']; ?>" value="" <?php echo $chequeado; ?>></td>
						<td class="celda-espacio-left"><?php echo $pmi['detalle'] ?></td>
						<td class="celda-espacio-left"><textarea class="<?php echo 'obs obs_'.$pmi['id']; ?>" data-id="<?php echo $pmi['id']; ?>" name="" id="" cols="30" rows="1" style="width: 99%;" <?php echo $deshabilitado; ?> ><?php echo $obs; ?></textarea></td>
					</tr>	
			<?php } ?>
			</tbody>
		</table>

		<hr class="margen-arriba-10">
		<div class="derecha-texto margen-arriba-10" style="margin-bottom: 10px;">
			<input class="botones btn-cancelar cerrar-lista-check" type="button" value="Cerrar">
		</div>

	</div>

<div id="mensaje"></div>
</div>



<script src="js/lista_check_plan_de_accion.js"></script>