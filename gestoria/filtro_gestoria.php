<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$cadena=' WHERE ';

	if ($idsucursal!=0) {
		$cadena .=' id_sucursal ='. $idsucursal.' AND ';
	}

	if ($inscripto==0) {
		$cadena .=" fec_ins IS NULL";
		$cadena .=" AND fec_rec_gestoria >= '".$fecha_desde."' AND fec_rec_gestoria <= '".$fecha_hasta."'";
		$SQL="SELECT * FROM view_registros_gestoria ".$cadena." ORDER BY id_reg_gestoria DESC";
		$res_reg = mysqli_query($con, $SQL);
		$cant_reg=mysqli_num_rows($res_reg);
		$cadena=$SQL;?>
		<input type="hidden" name="sql_filtro" id="sql_filtro"  value='<?php echo $cadena; ?>'>
		<div class='imp_rep ancho-100'>
			<div class=''>
				Registro Encontrados: <?php echo ' '.$cant_reg; ?>
			</div>
			<div class=''>
				Reportes por (Sin Usados): <span class="rep_sucursal" data-datos="<?php echo $fecha_desde.'/'.$fecha_hasta.'/'.$idsucursal.'/'.$inscripto; ?>">Resumen</span>
				<span class="rep_provincia" data-datos="<?php echo $fecha_desde.'/'.$fecha_hasta.'/'.$idsucursal.'/'.$inscripto; ?>"> Detalle</span>
			</div>
		</div>		
		<?php 
		include('contenido_cuerpo.php');
	}

	if ($inscripto==1) {
		$cadena .=" fec_ins IS NOT NULL";
		$cadena .=" AND fec_ins >= '".$fecha_desde."' AND fec_ins <= '".$fecha_hasta."'";
		$SQL="SELECT * FROM view_registros_gestoria ".$cadena." ORDER BY fec_ins DESC";
		$cadena=$SQL;
		$res_reg = mysqli_query($con, $SQL);
		$cant_reg=mysqli_num_rows($res_reg);
	
 ?>
 		<div class='imp_rep ancho-100'>
			<div class=''>
				Registro Encontrados: <?php echo ' '.$cant_reg; ?>
			</div>
			<div class=''>
				Reportes por (Sin Usados): <span class="rep_sucursal" data-datos="<?php echo $fecha_desde.'/'.$fecha_hasta.'/'.$idsucursal.'/'.$inscripto; ?>">Resumen</span>
				 <span class="rep_provincia" data-datos="<?php echo $fecha_desde.'/'.$fecha_hasta.'/'.$idsucursal.'/'.$inscripto; ?>""> Detalle</span>
			</div>
		</div>
 <input type="hidden" name="sql_filtro" id="sql_filtro"  value='<?php echo $cadena; ?>'>
	<table class="listado_gestoria">
		<colgroup>
				<col width="2%">
				<col width="3.5%">
				<!-- <col width="2%"> -->
				<col width="9%">
				<col width="2%">
				<col width="2%">
				<col width="8%">
				<col width="2%">
				<col width="3%">
				<col width="3.5%">
				<col width="2%">
				<col width="2%">
				<col width="2%">
				
		</colgroup>
		<thead>
			<tr>
				<td>N° Leg.</td>
				<td>Sucursal</td>
				
				<!-- <td>N° Rva.</td> -->
				<td>Cliente</td>
				<td>Compra</td>
				<td>Interno</td>
				<td>Versión</td>
				<td>Asesor</td>
				<td>Fec.Insc.</td>
				<td>Loc. Registro</td>
				<td>Patente</td>
				<td>CLI</td>
				<td>LEG</td>
			</tr>
		</thead>
		<tbody class="lista-tramites">
			<?php 
			while ($reg=mysqli_fetch_array($res_reg)) { ?>
				<tr>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['nro_leg']; ?></a></div></td>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['sucursal']; ?></a></div></td>
					<td><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['nombre']; ?></a></td>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php if ($reg['compra']==1) { echo 'Nuevo';	}else{echo 'Usado';} ?></a></div></td>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['interno']; ?></a></div></td>
					<td><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['modelo'].' '.$reg['version'].' '.$reg['usado']; ?></a></td>
					<td><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['asesor']; ?></a></td>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo cambiarformatofecha($reg['fec_ins']); ?></a></div></td>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['localidad']; ?></a></div></td>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><?php echo $reg['patente']; ?></a></div></td>
					<?php if ($reg['estado_cli']=='1') { $class_estaddo_cli='icon-listo completo';}else{$class_estaddo_cli='icon-no-listo incompleto';} ?>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><span class="<?php echo $class_estaddo_cli; ?>"></span></div></td>
					<?php if ($reg['estado_reg']=='1') { $class_estaddo_cli='icon-listo completo';}else{$class_estaddo_cli='icon-no-listo incompleto';} ?>
					<td><div class="centrar-texto"><a href="<?php echo 'tramite.php?id='.$reg['id_reg_gestoria']; ?>"><span class="<?php echo $class_estaddo_cli; ?>"></span></div></td>
				</tr>
			<?php } ?>		
			
		</tbody>
	</table>

<?php } ?>

<script src="js/filtro_gestoria.js"></script>