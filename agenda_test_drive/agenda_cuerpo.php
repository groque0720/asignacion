<?php
	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	@session_start();
?>

<div class="cuerpo_agenda">

		<div class="zona-sucursal-canlendario ancho-20">

			<div class="centrar-texto titulo-sucursales">
				<span class="centrar-texto ">SUCURSALES</span>
			</div>

			<div class="zona-sucursal">

				<?php
					$SQL="SELECT * FROM sucursales";
					$sucursales=mysqli_query($con, $SQL);

					while ($sucursal=mysqli_fetch_array($sucursales)) {?>
						<div class='sucursal ancho-45 centrar-texto' data-id="<?php echo $sucursal['idsucursal']; ?>"><span class='nombre-sucursal centrar-texto'><?php echo $sucursal['sucursal']; ?></span></div>
					<?php } ?>
				<input type="hidden" id='sucursal-seleccionado' name='sucursal-seleccionado'>
			</div>

		</div>

		<div class='zona-modelos'>
			<div class="centrar-texto titulo-sucursales">
				<span class="centrar-texto ">MODELOS</span>
			</div>

			<div class="modelo" data-id="1">
				<img class="imagen-modelo " src="imagenes/corolla.png" alt="">
				<div>Corolla</div>
			</div>
			<div class="modelo" data-id="2">
				<img  class="imagen-modelo" src="imagenes/hilux.png" alt="">
				<div>Hilux</div>
			</div>
						<div class="modelo" data-id="3">
				<img class="imagen-modelo" src="imagenes/etios_5p.png" alt="">
				<div>Etios 5 Ptas</div>
			</div>
						<div class="modelo" data-id="4">
				<img class="imagen-modelo" src="imagenes/etios_sedan.png" alt="">
				<div>Etios Sedan</div>
			</div>
			<input type="hidden" id="modelo-seleccionado" name="modelo-seleccionado">
		</div>

		<div class="zona-agenda ancho-50">

			<div class="centrar-texto titulo-sucursales">
				<span class="centrar-texto ">AGENDA TEST DRIVE</span>
			</div>

			<div class="centrar-texto titulo-sucursales">
				<span class="">Fecha:</span>
				<input type="text" id="fecha" value="" placeholder="dd/mm/aaaa">
			</div>

			<div class='agenda' >

				<table>
					<colgroup>
						<col>
						<col>
						<col>
						<col class="centrar-texto">
					</colgroup>
					<thead>
						<tr >
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

						while ($horario = mysqli_fetch_array($horarios)) { ?>

							<tr class="agendar" data-id="<?php echo $horario['id_linea'] ?>">
								<td class="centrar-texto "><?php echo $horario['horario']; ?></td>
								<td>-</td>
								<td>-</td>
								<td class="centrar-texto "><span class="icon-calendar agendar"></span></td>
							</tr>

						<?php } ?>
					</tbody>
				</table>

			</div>

		</div>
</div>