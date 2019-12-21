<?php 
@session_start();
$p=$_SESSION["idperfil"]; //id perfil
// 3 = asesores;
?>

<table class="listado_gestoria">
	<colgroup>
			<col width="7%"> <!-- Fec. Alta-->
			<col width="7%"><!-- Hora Alta-->
			<col width="5%"><!-- Nro Un.-->
			<col width="7%"><!-- Mes-->
			<col width="5%"><!-- Año-->
			<col width="5%"><!-- Interno-->
			<col width="7%"><!-- Nro Orden-->
			<col width="20%"><!-- Modelo Versión-->
			<col width="7%"><!-- Fec. Reserva-->
			<col width="7%"><!-- Hora Reserva-->
			<col width="15%"><!-- Cliente-->
			<col width="10%"><!-- Asesor-->
	</colgroup>
	<thead>
		<tr>
			<td>Fec. Alta</td>
			<td>Hora Alta</td>
			<td>Nro Un.</td>
			<td>Mes</td>
			<td>Año</td>
			<td>Interno</td>
			<td>Nro Orden</td>
			<td>Modelo / Versión</td>
			<td>Fec. Rva</td>
			<td>Hora Rva</td>
			<td>Cliente</td>
			<td>Asesor</td>
		</tr>
	</thead>
	<tbody class="lista-unidades">
	
	<?php 

	$fila=0;
	while ( $unidad=mysqli_fetch_array($unidades)) { $fila++; $libre = '';?>

		<tr class="">
			<td class="centrar-texto "><?php echo cambiarFormatoFecha($unidad['fec_alta']); ?></td>
			<td class="centrar-texto "><?php echo cambiarFormatoHora($unidad['hora_alta']); ?></td>
			<td class="centrar-texto "><?php echo $unidad['nro_unidad']; ?></td>
			<td class="centrar-texto "><?php echo $unidad['mes']; ?></td>
			<td class="centrar-texto "><?php echo $unidad['ano']; ?></td>
			<td class="centrar-texto "><?php echo $unidad['interno']; ?></td>
			<td class="centrar-texto "><?php echo $unidad['nro_orden']; ?></td>
			<td class="celda-espacio-left "><?php echo $unidad['modelo']. " ".$unidad['version']; ?></td>
			<td class="centrar-texto "><?php echo cambiarFormatoFecha($unidad['fec_reserva']); ?></td>
			<td class="centrar-texto "><?php echo $unidad['hora']; ?></td>
			<td class="celda-espacio-left "><?php echo $unidad['cliente']; ?></td>
			<td class="celda-espacio-left  "><?php echo $unidad['asesor']; ?></td>
		</tr>

	<?php } ?>

	</tbody>
</table>
<script src="js/app.js"></script>