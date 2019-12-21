	<?php

	$SQL="SELECT * FROM view_evaluaciones_usuarios WHERE id_evaluacion =".$id_evaluacion." AND id_usuario = ".$_SESSION["id_usuario"]." ORDER BY fecha DESC";
	$evals=mysqli_query($con, $SQL);
	?>
	<div class="zona-tabla ancho-60 s-100">

	 <div class="margen-auto ancho-100 s-100">
	 	<h1>Evaluación Personal</h1>
	 	<br>
	 </div>


		<table class="ancho-100">
			<colgroup>
				<col width="10%">
				<col width="15%">
				<col width="20%">
				<col width="15%">
				<col width="7.5%">
				<col width="7.5%">
				<col width="7.5%">
				<col width="7.5%">
				<col width="10%">
			</colgroup>
			<thead>
				<tr>
					<td>Fecha</td>
					<td>Período</td>
					<td>Nombre</td>
					<td>Sector</td>
					<td colspan="2">Auto Eval.</td>
					<td colspan="2">Eval. Sup.</td>
					<td>Resultado</td>
				</tr>
			</thead>
			<tbody>

			<?php
				while ($eval=mysqli_fetch_array($evals)) { ?>

				<tr>
					<td class="centrar-texto"><?php echo cambiarFormatoFecha($eval['fecha']); ?></td>
					<td class="centrar-texto"><?php echo $eval['periodo']; ?></td>
					<td class="celda-espacio"><?php echo $eval['nombre']; ?></td>

					<?php
						$SQL="SELECT * FROM sectores WHERE id_sector = ".$eval['id_sector'];
						$sectores=mysqli_query($con, $SQL);
						$sector=mysqli_fetch_array($sectores);
					 ?>
					<td class="centrar-texto"><?php echo $sector['sector']; ?></td>

					<?php

						$SQL="SELECT * FROM evaluacion_usuario_calificacion WHERE id_evaluacion_usuario = ".$eval['id_evaluacion_usuario'];
						$res=mysqli_query($con, $SQL);

						$puntaje_auto=0;
						$puntaje_sup=0;
						$cant_item = 0;


						while ($reg=mysqli_fetch_array($res)) {
							if ($reg['calificacion_autoevaluacion'] != 0){
								$cant_item ++;
								$puntaje_auto = $puntaje_auto + $reg['calificacion_autoevaluacion'];
								$puntaje_sup = $puntaje_sup + $reg['calificacion_superior'];
							}
						}
					 ?>


					<td class="centrar-texto"><?php echo round($puntaje_auto/$cant_item, 1); ?></td>
					<td class="centrar-texto"><a href="evaluacion.php?id=<?php echo $eval['id_evaluacion_usuario'] ?>&e=auto"><i class="material-icons">search</i></a></td>
					<td class="centrar-texto"><?php echo round($puntaje_sup/$cant_item, 1); ?></td>
					<td class="centrar-texto"><a href="evaluacion.php?id=<?php echo $eval['id_evaluacion_usuario'] ?>&e=sup"><i class="material-icons">search</i></a></td>

					<?php
						if ($puntaje_auto!=0 AND $puntaje_sup!=0) {

							$promedio = $puntaje_sup / $cant_item;

							$resultado ='Pendiente';

							if ($promedio >=1 AND $promedio < 2) {
								$resultado ='Insatisfactorio';
							}
							if ($promedio >=2 AND $promedio < 3) {
								$resultado ='Razonable';
							}
							if ($promedio >=3 AND $promedio < 4) {
								$resultado ='Bueno';
							}
							if ($promedio >=4 ) {
								$resultado ='Muy Bueno';
							}

						}else{
							$resultado='Sin Calificar';
						}

					 ?>
					<td class="centrar-texto"><?php echo $resultado; ?></td>

				</tr>

			 <?php } ?>


			</tbody>
		</table>
	</div>

	<?php
	//compruebo si el empleado es responsable de un area

		$SQL="SELECT * FROM usuarios WHERE es_superior = 1 AND id_usuario = ".$_SESSION["id_usuario"];
		$res=mysqli_query($con, $SQL);

		if (mysqli_num_rows($res)>0) { ?>

			 <div class="margen-auto ancho-60 s-100">
				<hr>
				<br>
			 </div>

			 <div class="margen-auto ancho-60 s-100">
			 	<h1>Evaluación Personal a Cargo</h1>
			 	<br>
			 </div>

			 <?php

				$SQL="SELECT * FROM usuarios WHERE id_superior_sector = ".$_SESSION["id_usuario"]." AND id_usuario <> ".$_SESSION["id_usuario"];
				$usuarios=mysqli_query($con, $SQL);

			  ?>

			  <div class="zona-tabla ancho-60 s-100">

				<table class="ancho-100">
					<colgroup>
						<col width="10%">
						<col width="15%">
						<col width="20%">
						<col width="15%">
						<col width="7.5%">
						<col width="7.5%">
						<col width="7.5%">
						<col width="7.5%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<td>Fecha</td>
							<td>Período</td>
							<td>Nombre</td>
							<td>Sector</td>
							<td colspan="2">Auto Eval.</td>
							<td colspan="2">Eval. Sup.</td>
							<td>Resultado</td>
						</tr>
					</thead>
					<tbody>

					<?php


					$SQL="SELECT * FROM view_evaluaciones_usuarios WHERE id_evaluacion =".$id_evaluacion." AND id_superior_sector = ".$_SESSION["id_usuario"];
					$evals=mysqli_query($con, $SQL);


						while ($eval=mysqli_fetch_array($evals)) { ?>

						<tr>
							<td class="centrar-texto"><?php echo cambiarFormatoFecha($eval['fecha']); ?></td>
							<td class="centrar-texto"><?php echo $eval['periodo']; ?></td>
							<td class="celda-espacio"><?php echo $eval['nombre']; ?></td>

							<?php
								$SQL="SELECT * FROM sectores WHERE id_sector = ".$eval['id_sector'];
								$sectores=mysqli_query($con, $SQL);
								$sector=mysqli_fetch_array($sectores);
							 ?>
							<td class="centrar-texto"><?php echo $sector['sector']; ?></td>

							<?php

								$SQL="SELECT * FROM evaluacion_usuario_calificacion WHERE id_evaluacion_usuario = ".$eval['id_evaluacion_usuario'];
								$res=mysqli_query($con, $SQL);

								$puntaje_auto=0;
								$puntaje_sup=0;
								$cant_item = 0;

								while ($reg=mysqli_fetch_array($res)) {
									$cant_item ++;
									$puntaje_auto = $puntaje_auto + $reg['calificacion_autoevaluacion'];
									$puntaje_sup = $puntaje_sup + $reg['calificacion_superior'];
								}
							 ?>

							<td class="centrar-texto"><?php echo round($puntaje_auto/$cant_item, 1); ?></td>
							<td class="centrar-texto"><a href="evaluacion.php?id=<?php echo $eval['id_evaluacion_usuario'] ?>&e=auto"><i class="material-icons">search</i></a></td>
							<td class="centrar-texto"><?php echo round($puntaje_sup/$cant_item, 1); ?></td>
							<td class="centrar-texto"><a href="evaluacion.php?id=<?php echo $eval['id_evaluacion_usuario'] ?>&e=supauto"><i class="material-icons">search</i></a></td>

							<?php
								if ($puntaje_auto!=0 AND $puntaje_sup!=0) {

									$promedio = $puntaje_sup / $cant_item;

									$resultado ='Pendiente';

									if ($promedio >=1 AND $promedio < 2) {
										$resultado ='Insatisfactorio';
									}
									if ($promedio >=2 AND $promedio < 3) {
										$resultado ='Razonable';
									}
									if ($promedio >=3 AND $promedio < 4) {
										$resultado ='Bueno';
									}
									if ($promedio >=4 ) {
										$resultado ='Muy Bueno';
									}

								}else{
									$resultado='Sin Calificar';
								}

							 ?>
							<td class="centrar-texto"><?php echo $resultado; ?></td>

						</tr>

					 <?php } ?>


					</tbody>
				</table>
			</div>
	<?php }  ?>