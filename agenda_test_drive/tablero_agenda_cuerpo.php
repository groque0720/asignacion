<table>
	<colgroup>
		<col>
		<col>
		<col>
		<col class="centrar-texto">
	</colgroup>
	<thead>
		<tr>
			<td width="20%">Horario</td>
			<td width="45%">Cliente</td>
			<td width="25%">Asesor</td>
			<td width="10%"><span class="icon-calendar"></span></td>
		</tr>
	</thead>
	<tbody>
	<?php 

	$SQL="SELECT * FROM agenda_td_horarios";
	$horarios = mysqli_query($con, $SQL);
	$i=1;
	while ($periodo=mysqli_fetch_array($horarios)) {
		$periodo_a[$i]['periodo']=$periodo['horario'];
		$i++;
	}

	$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
	$usuarios=mysqli_query($con, $SQL);
	$usuario_a[0]['nombre']= '-';
	while ($usuario=mysqli_fetch_array($usuarios)) {
		$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
	}

		while ($horario = mysqli_fetch_array($res)) { ?>

			<tr class="agendar" data-id="<?php echo $horario['id_linea'] ?>">
				<td class="centrar-texto "><?php echo $periodo_a[$horario['id_horario']]['periodo']; ?></td>
				<td><?php echo $horario['cliente']; ?></td>
				<td><?php echo $usuario_a[$horario['id_asesor']]['nombre']; ?></td>
				<td class="centrar-texto" ><span data-id="<?php echo $horario['id_linea'] ?>" class="icon-calendar agendar"></span></td>
			</tr>

		<?php } ?>
	</tbody>
</table>

<script src="js/tablero_agenda.js"></script>