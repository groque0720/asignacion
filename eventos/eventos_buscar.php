<?php

	include("funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	$negocio = $_POST["tipo"];

	if ($negocio=="") {
		$SQL="SELECT * FROM eventos WHERE not isnull(titulo) ORDER BY id_evento DESC LIMIT 100";
		$eventos = mysqli_query($con, $SQL);
	}else{
		$SQL="SELECT * FROM eventos WHERE negocio ='".$negocio."' ORDER BY id_evento DESC LIMIT 100";
		$eventos = mysqli_query($con, $SQL);
	}


 ?>

<table class="tabla-default">
	<thead>
		<tr>
			<td width="2%">Nro</td>
			<td width="20%">Evento</td>
			<td width="7%">Fec. Inicio</td>
			<td width="7%">Fec. Fin</td>
			<td width="10%">Asistencia</td>
			<td width="10%">Ubicación</td>
			<td width="2%">Opción</td>
		</tr>
	</thead>
	<tbody>
		<?php
			while ($evento=mysqli_fetch_array($eventos)) { ?>
			<tr>
				<td><div class="centrar-texto"><?php echo $evento["id_evento"]; ?></td></div>
				<td><?php echo $evento["titulo"];?></td>
				<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($evento["fecha_inicio"]);?></div>	</td>
				<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($evento["fecha_fin"]);?></div> </td>
				<td><div class="centrar-texto"><?php echo $evento["negocio"]; ?></div></td>
				<td><div class="centrar-texto"><?php echo $evento["ubicacion"]; ?></div></td>
				<td><div class="centrar-texto"><a class="icon-menu espacio" href="<?php echo "evento.php?id=".$evento["id_evento"]?>"></a></div></td>
			</tr>

			<?php } ?>

	</tbody>
</table>

<?php mysqli_close($con);	 ?>