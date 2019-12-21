<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);
@session_start();

if ($_SESSION["es_gerente"]==1) {

	$SQL="SELECT * FROM prospectos_clientes WHERE id_estado_cliente = 1 AND guardado = 1 ORDER BY nombre DESC LIMIT $inicio, $final";
	$clientes=mysqli_query($con, $SQL);

}else{
	$SQL="SELECT * FROM prospectos_clientes WHERE id_estado_cliente = 1 AND guardado = 1 AND id_usuario = {$_SESSION["id"]} ORDER BY nombre DESC LIMIT $inicio, $final";
	$clientes=mysqli_query($con, $SQL);
}

echo $inicio. ' '.$final;
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

$SQL="SELECT * FROM preferencias_contactos";
$preferencias_contactos=mysqli_query($con, $SQL);
$pref_contacto_a[0]['preferencia']= '-';
while ($pref_contacto=mysqli_fetch_array($preferencias_contactos)) {
	$pref_contacto_a[$pref_contacto['id']]['preferencia']= $pref_contacto['preferencia'];
}

$SQL="SELECT * FROM prospectos_clientes_estados";
$prospectos_clientes_estados=mysqli_query($con, $SQL);
$cliente_estado_a[0]['estado_cliente']= '-';
while ($cliente_estado=mysqli_fetch_array($prospectos_clientes_estados)) {
	$cliente_estado_a[$cliente_estado['id']]['estado_cliente']= $cliente_estado['estado_cliente'];
}

 ?>

<?php 
	while ($cliente=mysqli_fetch_array($clientes)) { ?>


	<tr class="fila" data-id="<?php echo $cliente['id']; $id=$cliente['id'];?>">
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $cliente['id']; ?></td>
		<td class="celda espacio-5-izq"	data-id="<?php echo $id; ?>"><?php echo "<span>  </span>".strtoupper($cliente['nombre']); ?></td>
		<?php if ($cliente['telefono']!='') {
			$espacio = ' - ';
		}else{
			$espacio = '';
			} ?>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $cliente['telefono'].$espacio.$cliente['celular']; ?></td>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $cliente['email']; ?></td>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $pref_contacto_a[$cliente['id_pref_contacto']]['preferencia'] ; ?></td>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo strtoupper($provincia_a[$cliente['id_provincia']]['provincia']); ?></td>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $localidad_a[$cliente['id_localidad']]['localidad']; ?></td>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $cliente_estado_a[$cliente['id_estado_cliente']]['estado_cliente']; ?></td>
		<td class="centrar-texto celda"	data-id="<?php echo $id; ?>"><?php echo $usuario_a[$cliente['id_usuario']]['nombre']; ?></td>
		<td class="centrar-texto" data-id="<?php echo $id; ?>" data-nombre="<?php echo $cliente['nombre']; ?>" data-tel="<?php echo $cliente['telefono']."' - '".$cliente['celular']; ?>" id="realizar_prospecto"><a href="" ></a><span class="icon-phone"></span></td>
	<!-- <td>Observación</td> -->

	</tr>
<?php } ?>


<!-- <div class="zona_ver_mas">
	<span id="boton_ver_mas" class="icon-search cursor-pointer" data-usu="recepcion" data-ini="0" data-cantidad="<?php echo $cantidad; ?>"> Ver más</span>
	<img id="imagen_carga" src="imagenes/cargando.gif" alt="">

</div> -->

<script src="js/dato_contenido.js"></script>