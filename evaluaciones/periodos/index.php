<?php

include('../z_comun/vista.php');

@session_start();



//if ($_SESSION['acceso_total'] == 1) {

	//$SQL="SELECT * FROM view_evaluaciones_usuarios WHERE id_usuario = ".$_SESSION["id_usuario"]." ORDER BY fecha DESC";
	//$evals=mysqli_query($con, $SQL);

	//}else{


	$SQL="SELECT * FROM evaluaciones ORDER BY fecha DESC";
	$evals=mysqli_query($con, $SQL);

	//}
?>

<div class="zona-tabla ancho-50 s-100">

	<table class="ancho-100">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="20%">
		</colgroup>
		<thead>
			<tr>
				<td>Fecha</td>
				<td>Período</td>
				<td>Ver</td>
			</tr>
		</thead>
		<tbody>
		<?php
			while ($eval=mysqli_fetch_array($evals)) { ?>

			<tr>
				<td class="centrar-texto"><?php echo cambiarFormatoFecha($eval['fecha']); ?></td>
				<td class="centrar-texto"><?php echo $eval['periodo']; ?></td>
				<td class="centrar-texto"><a href="evaluaciones.php?id=<?php echo $eval['id_evaluacion'] ?>"><i class="material-icons">search</i></a></td>
			</tr>

		 <?php } ?>


		</tbody>
	</table>
</div>