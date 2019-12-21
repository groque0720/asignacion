<?php
// include('../z_comun/vista.php');
include_once("../z_comun/funciones/funciones.php");
conectar();
extract($_POST);

$cadena_estado="";
$observacion = "";


// if ($comentario == 2) {
// 	$observacion = " (observacion_auto = '' OR observacion_superior = '')  AND ";
// }
// if ($comentario == 3) {
// 	$observacion = " (observacion_auto <> '' OR observacion_superior <> '')  AND ";
// }

if ($estado==2 AND $miembro==1) {
	$cadena= " terminado_autoevaluacion = 1 AND terminado_superior = 1 AND ";
}
if ($estado==2 AND $miembro==2) {
	$cadena= " terminado_autoevaluacion = 1 AND ";
}
if ($estado==2 AND $miembro==3) {
	$cadena= " terminado_superior = 1 AND ";
}

if ($estado==3 AND $miembro==1) {
	$cadena= " (terminado_autoevaluacion = 0 OR terminado_superior = 0) AND ";
}
if ($estado==3 AND $miembro==2) {
	$cadena= " terminado_autoevaluacion = 0 AND ";
}
if ($estado==3 AND $miembro==3) {
	$cadena= " terminado_superior = 0 AND ";
}

$cadena = $observacion.' '.$cadena;


 ?>
<div class="zona-tabla ancho-95 s-100" style="overflow-y: scroll; padding-bottom: 0; padding-top: -40px; ">
	<table class="ancho-95">
		<colgroup>
			<col width="9.6%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
			<col width="4.1%">
		</colgroup>
		<thead >
			<tr>
				<td rowspan="2">Nombre</td>
				<?php
					$SQL="SELECT titulo FROM evaluacion_item";
					$items=mysqli_query($con, $SQL);
					while ($item=mysqli_fetch_array($items)) { ?>
						<td colspan="2"><?php echo $item['titulo']; ?></td>
					<?php } ?>
				<td colspan="2">Resultado</td>
			</tr>
			<tr>
				<?php
					$SQL="SELECT id_item, titulo FROM evaluacion_item";
					$items=mysqli_query($con, $SQL);
					while ($item=mysqli_fetch_array($items)) { ?>
						<td>Auto</td>
						<td>Sup.</td>

						<?php
							$array[$item['id_item']]['auto']=0;
							$array[$item['id_item']]['sup']=0;
						 ?>

					<?php } ?>
						<td>Auto</td>
						<td>Sup.</td>
			</tr>
		</thead>
	</table>
</div>
<div class="zona-tabla ancho-95 s-100" style="height: 500px; overflow-y: auto; overflow-x: hidden;">
		<table class="ancho-95" style="margin-top: -100px;">
			<colgroup>
				<col width="9.6%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">
				<col width="4.1%">			</colgroup>
			<thead style="height: 0px;">
				<tr>
					<td rowspan="2">Nombre</td>
					<?php
						$SQL="SELECT titulo FROM evaluacion_item";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td colspan="2"><?php echo $item['titulo']; ?></td>
						<?php } ?>
					<td colspan="2">Resultado</td>
				</tr>
				<tr>
					<?php
						$SQL="SELECT id_item, titulo FROM evaluacion_item";
						$items=mysqli_query($con, $SQL);
						while ($item=mysqli_fetch_array($items)) { ?>
							<td>Auto</td>
							<td>Sup.</td>

							<?php
								$array[$item['id_item']]['auto']=0;
								$array[$item['id_item']]['sup']=0;
							 ?>

						<?php } ?>
							<td>Auto</td>
							<td>Sup.</td>
				</tr>
			</thead>
			<tbody>

				<?php
					$SQL="SELECT id_usuario, nombre, id_evaluacion_usuario, superior, sector, sucursal FROM view_evaluaciones_usuarios WHERE ".$cadena ." id_evaluacion = ".$id_evaluacion." ORDER BY nombre";// LIMIT 50";
					$empleados=mysqli_query($con, $SQL);
					$cant_emp=0;

					while ($emple=mysqli_fetch_array($empleados)) { $cant_emp++;?>
						<tr>
							<td><span  style="cursor: pointer;" title="<?php echo  $emple['sector'].' - Sup. '.$emple['superior'].' - '.$emple['sucursal'] ?> "><?php echo $emple['nombre']; ?></span></td>

							<?php
								$SQL="SELECT id_item FROM evaluacion_item";
								$items=mysqli_query($con, $SQL);
								$cant_item=0;
								$auto=0;
								$sup=0;
								// $array_valores=[];
								while ($item=mysqli_fetch_array($items)) { $cant_item++;

									$SQL="SELECT calificacion_autoevaluacion as auto, calificacion_superior as sup, observacion_auto, observacion_superior FROM evaluacion_con_valor_item WHERE id_usuario =".$emple['id_usuario']." AND id_evaluacion =". $id_evaluacion." AND id_item = ".$item['id_item'];
									$resultados=mysqli_query($con, $SQL);
									while ($resul=mysqli_fetch_array($resultados)) { ?>
										<td class="centrar-texto" style="">
											<div style="position: relative;"
											>
												<div>
													<?php echo $resul['auto']; $auto=$auto+$resul['auto'];?>
												</div>

												<div style="position: absolute; right: 5px; top: 0">
												<?php
													if ($resul['observacion_auto']!='') { ?>
														<span style="float: right; cursor: pointer;" title="<?php echo $resul['observacion_auto']; ?>"><img style="width: 12px;" src="../z_comun/imagenes/obs.png" alt=""></span>
												<?php }  ?>
												</div>
											</div>
										</td>
										<td class="centrar-texto" style="background: #F1F1F1; color: black;">
											<div style="position: relative;"
											>
												<div>
													<?php echo $resul['sup']; $sup=$sup+$resul['sup']; ?>
												</div>

												<div style="position: absolute; right: 5px; top: 0">
												<?php
													if ($resul['observacion_superior']!='') { ?>
														<span style="float: right; cursor: pointer;" title="<?php echo $resul['observacion_superior']; ?>"><img style="width: 12px;" src="../z_comun/imagenes/obs.png" alt=""></span>
												<?php }  ?>
												</div>
											</div>
											</td>

										<?php
											$array[$cant_item]['auto']=$array[$cant_item]['auto'] + $resul['auto'];
											$array[$cant_item]['sup']=$array[$cant_item]['sup'] + $resul['sup'];
										 ?>



									<?php } ?>

							<?php } ?>
									<td class="centrar-texto" style="font-weight: bold; color: red;">
										<a style="color:red;" target="_blank" href="<?php  echo 'evaluacion.php?id='.$emple['id_evaluacion_usuario'].'&e=auto'; ?>"><?php echo number_format($auto/$cant_item,1) ?></a>

										</td>
									<td class="centrar-texto" style="font-weight: bold; color: red; background: #EFEDED;">
										<a  style="color:red;" target="_blank" href="<?php  echo 'evaluacion.php?id='.$emple['id_evaluacion_usuario'].'&e=sup'; ?>"><?php echo number_format($sup/$cant_item,1); ?></a>

										</td>






										</td>

						</tr>
				<?php } ?>
					<tr class="">
						<td style="border: none;padding: 3px;"></td>
					</tr>

						<tr class="" style="font-weight: bold;">
							<td>Total por Item</td>

							<?php
								$SQL="SELECT id_item FROM evaluacion_item";
								$items=mysqli_query($con, $SQL);
								$cant_item=0;
								$auto=0;
								$sup=0;
								while ($item=mysqli_fetch_array($items)) { $cant_item++;?>
										<td class="centrar-texto"><?php echo number_format($array[$cant_item]['auto']/$cant_emp,2);
										 $auto=$auto+$array[$cant_item]['auto']/$cant_emp;?></td>
										<td class="centrar-texto" style="background: #F1F1F1; color: black;"><?php echo number_format($array[$cant_item]['sup']/$cant_emp,2); $sup=$sup+$array[$cant_item]['sup']/$cant_emp;?></td>
								<?php } ?>
									<td style="color:red;" class="centrar-texto" style="font-weight: bold; color: black;"><?php echo number_format($auto/$cant_item,1) ?></td>
									<td style="color:red;" class="centrar-texto" style="font-weight: bold; color: black; background-color: #EFEDED;"><?php echo number_format($sup/$cant_item,1); ?></td>

						</tr>

			</tbody>
		</table>


</div>
<?php echo $cadena;?>