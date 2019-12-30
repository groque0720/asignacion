<?php
	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
	@session_start();
	$perfil=$_SESSION["idperfil"];



$cad="";
$where = 0;

if ($id_sucursal!=0) {
	$cad = $cad ." WHERE id_sucursal = ".$id_sucursal;
	$where = 1;
}

if ($id_asesor !=0 ) {
	$cad = $cad . " AND id_asesor = ". $id_asesor;
}

if($id_modelo !=0) {

	if ($where == 1) {
		$cad = $cad." AND id_modelo = ".$id_modelo;
	}else{
		$cad = $cad." WHERE id_modelo = ".$id_modelo;
	}
}

if($id_mes !=0) {

	if ($where == 1) {
		$cad = $cad." AND MONTH(fecha) = ".$id_mes." AND YEAR(fecha) = ".$anio;
	}else{
		$cad = $cad." WHERE MONTH(fecha) = ".$id_mes." AND  YEAR(fecha) = ".$anio;
	}
}else{
	if ($where == 1) {
		$cad = $cad." AND YEAR(fecha) = ".$anio;
	}else{
		$cad = $cad." WHERE YEAR(fecha) = ".$anio;
	}
}





$cad=$cad." ORDER BY asesor, fecha ASC, horario ASC";



	$SQL="SELECT * FROM view_agenda_td ".$cad;// WHERE cliente <> null";
	$tds=mysqli_query($con, $SQL);

 ?>

<!-- <div class="flex padding-5 justificar-flex">
	<div class="ancho-45" ></div>
	<div class="ancho-45 derecha-texto">Dos</div>
</div> -->
<table class="listado_gestoria">
	<colgroup>
			<col width="10%">
			<col width="10%">
			<col width="12%">
			<col width="20%">
			<col width="20%">
			<col width="15%">
			<col width="10%">
	</colgroup>
	<thead>
		<tr>
			<td>Fecha</td>
			<td>Horario</td>
			<td>Modelo</td>
			<td>Cliente</td>
			<td>Teléfono</td>
			<td>Asesor</td>
			<td>Form. TD.</td>

		</tr>
	</thead>
	<tbody class="lista-unidades">

	<?php

	$bloqueado='';
	if ($perfil==3) {
		$bloqueado = 'disabled';
	}


	$fila=0;
	while ( $td=mysqli_fetch_array($tds)) { $fila++; $libre = '';?>

	<?php
		if ($td['ok']==1) {
			$class_fila='fila-ok';
		}else{
			$class_fila='fila-no-ok';
		}
	?>

		<tr class="<?php echo 'fila_'.$fila.' '.$class_fila; ?>">
			<td class="centrar-texto "><?php echo cambiarFormatoFecha($td['fecha']); ?></td>
			<td class="centrar-texto "><?php echo $td['horario']; ?></td>
			<td class="centrar-texto "><?php echo $td['modelo']; ?></td>
			<td class=""><?php echo $td['cliente']; ?></td>
			<td class="centrar-texto "><?php echo $td['telefono']; ?></td>
			<td class="centrar-texto "><?php echo $td['asesor']; ?></td>
			<td class="centrar-texto "><input class="checkbox_ok" data-fila ='<?php echo $fila ?>' data-id="<?php echo $td['id_linea'] ?>" type="checkbox" <?php if ($td['ok']==1) { echo 'checked';	} ?> <?php echo $bloqueado; ?>></td>
		</tr>

	<?php } ?>

	</tbody>
</table>

<script src="js/informe_cuerpo.js"></script>