<?php 

$SQL="SELECT * FROM sucursales";
$sucursales=mysqli_query($con, $SQL);
$sucursal_a[0]['sucursal']= '-';
while ($sucursal=mysqli_fetch_array($sucursales)) {
	$sucursal_a[$sucursal['idsucursal']]['sucursal']= $sucursal['sucursal'];
}


$SQL="SELECT * FROM recepcion_modo_acercamiento";
$acercamientos=mysqli_query($con, $SQL);
$acercamiento_a[0]['acercamiento']= '-';
while ($acercamiento=mysqli_fetch_array($acercamientos)) {
	$acercamiento_a[$acercamiento['id_modo_acercamiento']]['acercamiento']= $acercamiento['modo_acercamiento'];
}

$SQL="SELECT * FROM provincias";
$provincias=mysqli_query($con, $SQL);
$provincia_a[0]['provincia']= '-';
while ($provincia=mysqli_fetch_array($provincias)) {
	$provincia_a[$provincia['id_provincia']]['provincia']= $provincia['provincia'];
}

$SQL="SELECT * FROM recepcion_localidades";
$localidades=mysqli_query($con, $SQL);
$localidad_a[0]['localidad']= '-';
while ($localidad=mysqli_fetch_array($localidades)) {
	$localidad_a[$localidad['id_localidad']]['localidad']= $localidad['localidad'];
}

$SQL="SELECT * FROM usuarios";
$usuarios=mysqli_query($con, $SQL);
$usuario_a[0]['nombre']= '-';
$i=1;
while ($usuario=mysqli_fetch_array($usuarios)) {
	$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
	$i++;
}

$SQL="SELECT * FROM grupos";
$grupos=mysqli_query($con, $SQL);
$grupo_a[0]['grupo']= '-';
$i=1;
while ($grupo=mysqli_fetch_array($grupos)) {
	$grupo_a[$grupo['idgrupo']]['grupo']= $grupo['grupo'];
	$i++;
}

$SQL="SELECT * FROM modelos";
$modelos=mysqli_query($con, $SQL);
$modelo_a[0]['modelo']= '-';
while ($modelo=mysqli_fetch_array($modelos)) {
	$modelo_a[$modelo['idmodelo']]['modelo']= $modelo['modelo'];
}

 ?>

<table class="listado_gestoria">
	<colgroup>
			<col width="2%">
			<col width="1%">
			<col width="2.5%">
			<col width="2%">
			<col width="6%">
			<col width="2%">
			<col width="3.5%">
			<col width="3%">
			<col width="6.5%">
			<col width="1.5%">
			<col width="2.5%">
			<col width="2%">
			<col width="2%">
			<col width="2%">
			<col width="2%">
			<col width="1%">
			<!-- <col width="3.5%"> -->
	</colgroup>
	<thead>
		<tr>
			<td>Fecha</td>
			<td>Hora</td>
			<td>Sucursal</td>
			<td>Modo</td>
			<td>Cliente</td>
			<td>Provincia</td>
			<td>Localidad</td>
			<td>Modelo</td>
			<td>Versión</td>
			<td>Derivado</td>
			<td>Asesor</td>
			<td>Visto</td>
			<td>Reg.</td>
			<td>Seg.</td>
			<td>Listo</td>
			<!-- <td>Observación</td> -->
		</tr>
	</thead>
	<tbody class="lista-unidades">

	<?php 
		while ($recepcion=mysqli_fetch_array($recepcions)) { ?>
		<tr class="fila" data-id="<?php echo $recepcion['id_recepcion']; ?>">
			<td class="centrar-texto"><?php echo cambiarFormatoFecha($recepcion['fecha']); ?></td>
			<td class="centrar-texto"><?php echo cambiarFormatohora($recepcion['hora']); ?></td>
			<td class="centrar-texto"><?php echo $sucursal_a[$recepcion['id_sucursal']]['sucursal']; ?></td>
			<td class="centrar-texto"><?php echo $acercamiento_a[$recepcion['id_acercamiento']]['acercamiento']; ?></td>
			<td class="espacio-5-izq"><?php echo "<span>  </span>".$recepcion['cliente']; ?></td>
			<td class="centrar-texto"><?php echo $provincia_a[$recepcion['id_provincia']]['provincia']; ?></td>
			<td class="centrar-texto"><?php echo $localidad_a[$recepcion['id_localidad']]['localidad']; ?></td>
			<td class="centrar-texto"><?php echo $grupo_a[$recepcion['id_grupo']]['grupo']; ?></td>
			<td class="espacio-5-izq"><?php echo "<span>  </span>".$modelo_a[$recepcion['id_modelo']]['modelo']; ?></td>
			<td class="centrar-texto"><?php if ($recepcion['derivado']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<td class="centrar-texto"><?php echo $usuario_a[$recepcion['id_asesor']]['nombre']; ?></td>
			<td class="centrar-texto"><?php if ($recepcion['visto']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<td class="centrar-texto"><?php if ($recepcion['carga_registro']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<td class="centrar-texto"><?php if ($recepcion['seguimiento']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<td class="centrar-texto"><?php if ($recepcion['terminado']==1) { echo 'Si';}else{ echo 'No';	} ?></td>
			<!-- <td>Observación</td> -->

		</tr>
	<?php } ?>

	</tbody>
</table>

<script src="js/recepcion_cuerpo.js"></script>