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

$SQL="SELECT * FROM grupos";
$modelos=mysqli_query($con, $SQL);
$modelo_a[0]['modelo']= '-';
while ($modelo=mysqli_fetch_array($modelos)) {
	$modelo_a[$modelo['idgrupo']]['modelo']= $modelo['grupo'];
}

$SQL="SELECT * FROM modelos";
$versiones=mysqli_query($con, $SQL);
$version_a[0]['version']= '-';
while ($version=mysqli_fetch_array($versiones)) {
	$version_a[$version['idmodelo']]['version']= $version['modelo'];
}

$SQL="SELECT * FROM modelos_tpa";
$modelos=mysqli_query($con, $SQL);
$modelo_a[0]['modelo']= '-';
while ($modelo=mysqli_fetch_array($modelos)) {
	$modelo_a[$modelo['id']]['modelo']= $modelo['modelo'];
}

 ?>

<table class="listado_gestoria" id='tabla-datos'>
	<colgroup>
			<col data-col="Nro Prospecto" width="2%">
			<col data-col="fecha" width="2%">
			<?php if ($_SESSION["es_gerente"]==1) {?>
				<col data-col="sucursal" width="2%">
			<?php } ?>
			<col data-col="modo" width="2.5%">
			<col data-col="canal" width="3.5%">
			<col data-col="cliente" width="6%">
			<col data-col="modelo" width="9%">
			<col data-col="derivado" width="2%">
			<?php if ($_SESSION["es_gerente"]==1) {?>
			<col data-col="asesor" width="2.5%">
			<col data-col="visto" width="2%">
			<?php } ?>
			<col data-col="prox_contacto" width="3.5%">
			<col data-col="estado" width="2%">
	</colgroup>
	<thead>
		<tr>
			<td>Prosp.</td>
			<td>Fecha</td>
			<?php if ($_SESSION["es_gerente"]==1) {?>
				<td>Sucursal</td>
			<?php } ?>
			<td>Modo</td>
			<td>Canal</td>
			<td>Cliente</td>
			<td>Modelo</td>
			<td>Derivado</td>
			<?php if ($_SESSION["es_gerente"]==1) {?>
				<td>Asesor</td>
				<td>Visto</td>
			<?php } ?>
			<td>Prox. Contacto</td>
			<td>Estado</td>
			<!-- <td>Observación</td> -->
		</tr>
	</thead>
	<tbody class="lista-unidades">
	<?php 
		while ($prospecto=mysqli_fetch_array($prospectos)) { ?>
			<?php 
				$clase='';
				$SQL="SELECT id, nombre, id_provincia, id_localidad FROM prospectos_clientes WHERE id = {$prospecto['id_cliente']}";
				$clientes= mysqli_query($con, $SQL);
				$cliente = mysqli_fetch_array($clientes);
			 ?>


		<tr class="fila" data-id="<?php echo $prospecto['id']; ?>">
			<td class="centrar-texto"><?php echo $prospecto['id']; ?></td>
			<td class="centrar-texto"><?php echo cambiarFormatoFecha($prospecto['fecha_carga']); ?></td>
			<?php if ($_SESSION["es_gerente"]==1) {?>
			<td class="centrar-texto"><?php echo $sucursal_a[$prospecto['id_sucursal']]['sucursal']; ?></td> 
			<?php } ?>
			<td class="centrar-texto"><?php echo $modo_a[$prospecto['id_modo_acercamiento']]['modo']; ?></td>
			<td class="centrar-texto"><?php echo $canal_a[$prospecto['id_canal_acercamiento']]['canal']; ?></td>

			<td class="espacio-5-izq"><?php echo "<span>  </span>".strtoupper($cliente['nombre']); ?></td>
			<td class="centrar-texto"><?php echo $modelo_a[$prospecto['id_modelo_tpa']]['modelo']; ?></td>
			<td class="centrar-texto"><?php if ($prospecto['derivado']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<?php if ($_SESSION["es_gerente"]==1) {?>
			<td class="centrar-texto"><?php echo $usuario_a[$prospecto['id_usuario']]['nombre']; ?></td>
			<td class="centrar-texto"><?php if ($prospecto['visto']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<?php } ?>
			<?php 
				$SQL="SELECT fec_contacto FROM prospectos_seguimientos WHERE realizado = 0 AND id_prospecto = {$prospecto['id']}";
				$prox_contactos = mysqli_query($con, $SQL);
				$cant_reg = mysqli_num_rows($prox_contactos);

				if ($cant_reg > 0) {
					$prox_contacto=mysqli_fetch_array($prox_contactos);

					if (date('Y-m-d')>=$prox_contacto['fec_contacto']) {
						$clase = 'contactar_hoy';
					}
					 ?>
					 <td class="<?php echo 'centrar-texto '.$clase; ?>"><?php echo cambiarFormatoFecha($prox_contacto['fec_contacto']); ?></td>
				<?php }else{
					$prox_contacto['fec_contacto']='Sin Próx. Contacto'; ?>
					<td class="centrar-texto"><?php echo $prox_contacto['fec_contacto']; ?></td>
				<?php }  ?>

			
			<td class="centrar-texto"><?php if ($prospecto['cerrado']==1) { echo 'Cerrado';}else{ echo 'Abierto';	} ?></td>
			<!-- <td>Observación</td> -->

		</tr>
	<?php } ?>

	</tbody>
</table>

<!-- <div class="zona_ver_mas">
	<span id="boton_ver_mas" class="icon-search cursor-pointer" data-usu="recepcion" data-ini="0" data-cantidad="<?php echo $cantidad; ?>"> Ver más</span>
	<img id="imagen_carga" src="imagenes/cargando.gif" alt="">

</div> -->

<script src="js/prospecto_contenido.js"></script>