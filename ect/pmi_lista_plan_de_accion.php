
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT * FROM ect_pmi_asesores_pam WHERE id_asesor_ect = $id_asesor AND id_mes = $mes AND ano = $ano";
	$pmis=mysqli_query($con, $SQL);

 ?>
	<table class="ancho-95 margen-arriba-5">
<!-- 		<colgroup>
			<col width="50%">
		</colgroup> -->
		<thead>
			<tr>
				<td >Conceptos</td>
			</tr>
		</thead>
		<tbody>
		<?php 
			while ($pmi = mysqli_fetch_array($pmis)) { 

				$SQL="SELECT * FROM ect_pam_detalle WHERE id = ".$pmi['id_item'];
				$items=mysqli_query($con, $SQL);
				$item = mysqli_fetch_array($items);

				?>
			<tr>
				<td class="celda-espacio-left"><?php echo $item['detalle'].' '.$pmi['observacion']; ?></td>
			</tr>


		<?php  	}  ?>

		</tbody>

	</table>
