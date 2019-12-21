<?php 

$SQL="SELECT * FROM sucursales";
$sucursales=mysqli_query($con, $SQL);
$sucursal_a[0]['sucursal']= '-';
while ($sucursal=mysqli_fetch_array($sucursales)) {
	$sucursal_a[$sucursal['idsucursal']]['sucursal']= $sucursal['sucursal'];
}

$SQL="SELECT * FROM prospectos_modos_acercamientos";
$modos=mysqli_query($con, $SQL);
$modo_a[0]['modo']= '-';
while ($modo=mysqli_fetch_array($modos)) {
	$modo_a[$modo['id']]['modo']= $modo['modo'];
}

$SQL="SELECT * FROM prospectos_canales_acercamientos";
$canales=mysqli_query($con, $SQL);
$canal_a[0]['canal']= '-';
while ($canal=mysqli_fetch_array($canales)) {
	$canal_a[$canal['id']]['canal']= $canal['canal'];
}

$SQL="SELECT * FROM provincias";
$provincias=mysqli_query($con, $SQL);
$provincia_a[0]['provincia']= '-';
while ($provincia=mysqli_fetch_array($provincias)) {
	$provincia_a[$provincia['id_provincia']]['provincia']= $provincia['provincia'];
}

$SQL="SELECT * FROM localidades";
$localidades=mysqli_query($con, $SQL);
$localidad_a[0]['localidad']= '-';
while ($localidad=mysqli_fetch_array($localidades)) {
	$localidad_a[$localidad['id']]['localidad']= $localidad['localidad'];
}

$SQL="SELECT * FROM usuarios";
$usuarios=mysqli_query($con, $SQL);
$usuario_a[0]['nombre']= '-';

while ($usuario=mysqli_fetch_array($usuarios)) {
	$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
}

$SQL="SELECT * FROM modelos_tpa";
$modelos=mysqli_query($con, $SQL);
$modelo_a[0]['modelo']= '-';
while ($modelo=mysqli_fetch_array($modelos)) {
	$modelo_a[$modelo['id']]['modelo']= $modelo['modelo'];
}

$SQL="SELECT * FROM prospectos_seguimiento_resultados";
$resultados=mysqli_query($con, $SQL);
$resultado_a[0]['modelo']= '-';
while ($resultado=mysqli_fetch_array($resultados)) {
	$resultado_a[$resultado['id']]['resultado']= $resultado['resultado'];
}


 ?>

<table class="listado_gestoria" id='tabla-datos'>
	<colgroup>
			<col data-col="Nro Prospecto" width="2%">
			<col data-col="fecha" width="2%">
			<?php if ($_SESSION["es_gerente"]==1) {?>
				<col data-col="sucursal" width="3%">
				<col data-col="asesor" width="3%">
			<?php } ?>
			<col data-col="cliente" width="8%">
			<col data-col="plan de interes" width="7%">
			<col data-col="ultimo contacto" width="3%">
			<col data-col="resultado" width="3%">
			<col data-col="observacion" width="10%">
			<col data-col="prox_contacto" width="3%">
	</colgroup>
	<thead>
		<tr>
			<td>Prosp.</td>
			<td>Fecha</td>
			<?php if ($_SESSION["es_gerente"]==1) {?>
				<td>Sucursal</td>
				<td>Asesor</td>
			<?php } ?>
			<td>Cliente</td>
			<td>Plan de Interes</td>
			<td>Ult. Contacto</td>
			<td>Resultado</td>
			<td>Observación Ult. Contacto</td>
			<td>Prox. Contacto</td>
			<!-- <td>Observación</td> -->
		</tr>
	</thead>
	<tbody class="lista-unidades">
	<?php 
		while ($seguimiento=mysqli_fetch_array($seguimientos)) {

			$clase='';
			$SQL="SELECT * FROM prospectos WHERE guardado = 1 AND id = ".$seguimiento['id_prospecto'];
			$prospectos=mysqli_query($con, $SQL); 

			$prospecto = mysqli_fetch_array($prospectos);

			$SQL="SELECT id, nombre, id_provincia, id_localidad FROM prospectos_clientes WHERE id = {$prospecto['id_cliente']}";
			$clientes= mysqli_query($con, $SQL);
			$cliente = mysqli_fetch_array($clientes);

		 ?>

		<tr class="fila" data-id="<?php echo $prospecto['id']; ?>">
			<td class="centrar-texto"><?php echo $prospecto['id']; ?></td>
			<td class="centrar-texto"><?php echo cambiarFormatoFecha($prospecto['fecha_carga']); ?></td>
			<?php if ($_SESSION["es_gerente"]==1) {?>
			<td class="centrar-texto"><?php echo $sucursal_a[$prospecto['id_sucursal']]['sucursal']; ?></td> 
			<td class="centrar-texto"><?php echo $usuario_a[$prospecto['id_usuario']]['nombre']; ?></td>			
			<?php } ?>
			<td class="espacio-5-izq"><?php echo "<span>  </span>".strtoupper($cliente['nombre']); ?></td>
			<td class="centrar-texto"><?php echo $modelo_a[$prospecto['id_modelo_tpa']]['modelo']; ?></td>

			<?php 
			$SQL="SELECT * FROM prospectos_seguimientos WHERE guardado = 1 AND realizado = 1 AND id_prospecto = ".$prospecto['id']." ORDER BY fec_contacto LIMIT 1";
			$ultimos=mysqli_query($con, $SQL);
			$cant= mysqli_num_rows($ultimos);

			if ($cant>=1) {
				$ultimo = mysqli_fetch_array($ultimos); 
				?>
				<td class="centrar-texto"><?php echo cambiarFormatoFecha($ultimo['fec_realizado']); ?></td>
				<td class="centrar-texto"><?php echo $resultado_a[$ultimo['id_resultado']]['resultado']; ?></td>
				<td class="centrar-texto"><?php echo $ultimo['observacion']; ?></td>

			<?php }else{ ?>
				<td class="centrar-texto"><?php echo cambiarFormatoFecha($prospecto['fecha_alta']); ?></td>
				<td class="centrar-texto"><?php echo 'Primer Contacto'; ?></td>
				<td class="centrar-texto"><?php echo $prospecto['observacion']; ?></td>
			<?php } ?>



			<?php 
			if (date('Y-m-d')>=$seguimiento['fec_contacto']) {
				$clase = 'contactar_hoy';
			}
			 ?>
			<td class="<?php echo 'centrar-texto '.$clase; ?>"><?php echo cambiarFormatoFecha($seguimiento['fec_contacto']); ?></td>

			<!-- <td>Observación</td> -->

		</tr>
	<?php } ?>

	</tbody>
</table>



<script src="js/agenda_contacto_contenido.js"></script>