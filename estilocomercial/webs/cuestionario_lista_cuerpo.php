
<table class="tabla-default">
	<thead>
		<tr>
			<td width="4%">Fecha.Origen</td>
			<td width="4%">Fecha.Encuesta</td>
			<td width="1%">Encuesta</td>
			<td width="6%">Cliente</td>
			<td width="4%">Asesor</td>
			<td width="1%">Estado</td>
			<td width="7%">Comentario</td>
			<td width="1%"><div class="icon-buscar centar-texto"></div></td>
		</tr>
	</thead>

	<tbody>
		<?php while ($cuest=mysqli_fetch_array($res)) { ?>
		<tr>
			<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($cuest["fecha_muestra_origen"]) ?></div></td>
			<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($cuest["fecha_cuestionario"]) ?></div></td>
			<td>
				<div class="centrar-texto">
				<?php
					$SQL="SELECT * FROM encuestas WHERE id_encuesta=".$cuest["id_encuesta"];
					$res_e=mysqli_query($con, $SQL);
					if (empty($res_e)) {$encuesta["encuesta"]="";}else{$encuesta=mysqli_fetch_array($res_e);}
					echo $encuesta["encuesta"];
				 ?>
				</div>
			</td>
			<td>
				<?php
					$SQL="SELECT * FROM cuestionarios_clientes WHERE id_cliente_cuestionario=".$cuest["id_cliente_cuestionario"];
					$res_e=mysqli_query($con, $SQL);
					if (empty($res_e)) {$cliente["nombre"]="";}else{$cliente=mysqli_fetch_array($res_e);}
					// echo $cliente["nombre"];
				 ?>
				 <a href="<?php echo "cuestionario.php?id=".$cuest["id_cuestionario"]."&cue=".$cuest["id_encuesta"]; ?>"><?php echo $cliente["nombre"]; ?></a>
			</td>
			<td>
				<?php
					$SQL="SELECT * FROM usuarios WHERE id_usuario = ".$cuest["id_usuario"];
					$usu=mysqli_fetch_array(mysql_query($SQL));
					echo $usu["nombre"];

				 ?>
			</td>
			<td>
				<div class="centrar-texto">
				<?php
					$SQL="SELECT * FROM cuestionarios_estados WHERE id_estado_cuestionario=".$cuest["id_estado_cuestionario"];
					$res_e=mysqli_query($con, $SQL);
					if (empty($res_e)) {$estado["estado_cuestionario"]="";}else{$estado=mysqli_fetch_array($res_e);}
					echo $estado["estado_cuestionario"];
				 ?>
				</div>
			</td>
			<td><?php $comentario = str_replace ("_"," _ ",$cuest["comentario"]); echo $comentario;?></td>
			<td>
				<div class="centrar-texto">
					<a class="<?php if ($cuest["caracter"]==1) { echo "icon-pin req_obs";}else{ echo "icon-pin no_req_obs";} ?>" href="<?php echo "cuestionario.php?id=".$cuest["id_cuestionario"]."&cue=".$cuest["id_encuesta"]; ?>" target="_blank"></a>
				</div>
			</td>
		</tr>

		<?php } ?>

	</tbody>
</table>
