
<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);


$SQL="SELECT * FROM ect_view_asesores_activos";
$asesores=mysqli_query($con, $SQL);

	while ($asesor= mysqli_fetch_array($asesores)) {

		$SQL="SELECT id FROM ect_csi_asesores WHERE id_asesor_ect =".$asesor['id_asesor_ect']." AND id_mes = {$mes} AND ano = {$ano} ";
		$csi_asesores = mysqli_query($con, $SQL);

		$cant = mysqli_num_rows($csi_asesores);

		if ($cant == 0 ) {
			
			$SQL="INSERT INTO ect_csi_asesores (id_mes, ano, id_asesor_ect) VALUES ({$mes}, {$ano}, ".$asesor['id_asesor_ect'].")";
			mysqli_query($con, $SQL);
		}
	}

?>

<div id="tabla_csi_asesores">	

	<table class="ancho-50 margen-arriba-10">
		<colgroup>
				<col width="5%"> 
				<col width="5%">
				<col width="2%">
				<col width="2%">
		</colgroup>
		<thead>
			<tr>
				<td>Sucursal</td>
				<td>Asesor</td>
				<td>CSI</td>
				<td><span class="icon-search-plus"></span></td>
			</tr>
		</thead>
		<tbody class="">
			<?php 

				$SQL="SELECT * FROM sucursales";
				$sucursales=mysqli_query($con, $SQL);
				$sucursal_a[0]['sucursal']= '-';
				$i=1;
				while ($sucursal=mysqli_fetch_array($sucursales)) {
					$sucursal_a[$i]['sucursal']= $sucursal['sucursal'];
					$i++;
				}

				$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
				$usuarios=mysqli_query($con, $SQL);
				$usuario_a[1]['nombre']= '-';
				$i=1;
				while ($usuario=mysqli_fetch_array($usuarios)) {
					$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
					$usuario_a[$usuario['idusuario']]['id_sucursal']= $usuario['idsucursal'];
					$i++;
				}

				$SQL="SELECT * FROM ect_view_csi_asesores WHERE id_mes = {$mes} AND ano = {$ano}";
				$asesores=mysqli_query($con, $SQL);

			$fila=0;
			while ($csi_asesor = mysqli_fetch_array($asesores)) {$fila++; $columna_celda=3;?>

			<tr class="<?php echo 'filas fila_'.$fila; ?>">
				<td class="centrar-texto" ><?php echo $sucursal_a[$csi_asesor["id_sucursal"]]['sucursal']; ?></td>
				<td class="centrar-texto" ><?php echo $csi_asesor['asesor']; ?></td>
				<td class="centrar-texto"> <input data-id="<?php echo $csi_asesor['id']; ?>" data-nrofila="<?php echo $fila; ?>"  data-columna="<?php echo $columna_celda ?>" data-fila="<?php echo 'fila_'.$fila; ?>" class="<?php echo $fila.'-'.$columna_celda.' cuadro-input derecha-texto'; ?>" type="text" value="<?php if ($csi_asesor['csi']!=0) { echo $csi_asesor['csi']; } ; ?>" size="2"></td>
				<td class="centrar-texto"><a class="detalle_csi_asesor" data-asesor="<?php echo $csi_asesor['asesor']; ?>" data-id="<?php echo $csi_asesor['id']; ?>" href=""><span class="icon-search-plus"></span></a></td>
			</tr>

			 <?php } ?>

		</tbody> 
	</table>
</div>

<div id="tabla_csi_asesores_detalle">
	
</div>
<script src="js/csi_asesores_cuerpo.js"></script>