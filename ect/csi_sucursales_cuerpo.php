
<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);


$SQL="SELECT * FROM ect_sucursales";
$sucursales=mysqli_query($con, $SQL);

	while ($sucursal= mysqli_fetch_array($sucursales)) {

		$SQL="SELECT id FROM ect_csi_sucursales WHERE id_sucursal_ect =".$sucursal['id']." AND id_mes = {$mes} AND ano = {$ano} ";
		$csi_sucursales = mysqli_query($con, $SQL);

		$cant = mysqli_num_rows($csi_sucursales);

		if ($cant == 0 ) {
			
			$SQL="INSERT INTO ect_csi_sucursales (id_mes, ano, id_sucursal_ect) VALUES ({$mes}, {$ano}, ".$sucursal['id'].")";
			mysqli_query($con, $SQL);
		}

	}

?>

<div id="tabla_csi_sucursales">

	<table class="ancho-50 margen-arriba-10">
		<colgroup>
				<col width="40%"> 
				<col width="20%">
				<col width="10%">
				<col width="20%">
				<col width="10%">
		</colgroup>
		<thead>
			<tr>
				<td>Sucursal</td>
				<td colspan="2">CSI DYV</td>
				<td colspan="2">CSI TASA</td>
			</tr>
		</thead>
		<tbody class="">
			<?php 

				$SQL="SELECT * FROM ect_view_csi_sucursales WHERE id_mes = {$mes} AND ano = {$ano}";
				$sucursales=mysqli_query($con, $SQL);

			$fila=0;
			while ($csi_sucursal = mysqli_fetch_array($sucursales)) {$fila++; $columna_dyv=2; $columna_tasa=3;?>

			<tr class="<?php echo 'filas fila_'.$fila; ?>">
				<td class="centrar-texto" ><?php echo $csi_sucursal['sucursal']; ?></td>

				<td class="centrar-texto">
					 <input
					 	 data-csi="dyv"
					 	 data-id="<?php echo $csi_sucursal['id']; ?>"
					 	 data-nrofila="<?php echo $fila; ?>"
					 	 data-columna="<?php echo $columna_dyv ?>"
					 	 data-fila="<?php echo 'fila_'.$fila; ?>"
					 	 class="<?php echo $fila.'-'.$columna_dyv.' cuadro-input derecha-texto'; ?>"
					 	 type="text"
					 	 value="<?php if ($csi_sucursal['csi_dyv']!=0) { echo number_format($csi_sucursal['csi_dyv'],2); } ; ?>"
					 	 size="2">
				</td>



				<td class="centrar-texto"><a class="detalle_csi_suc_dyv" data-sucursal="<?php echo $csi_sucursal['sucursal']; ?>" data-id="<?php echo $csi_sucursal['id']; ?>" href=""><span class="icon-search-plus"></span></a></td>

				<td class="centrar-texto"> <input data-csi="tasa" data-id="<?php echo $csi_sucursal['id']; ?>" data-nrofila="<?php echo $fila; ?>"  data-columna="<?php echo $columna_tasa ?>" data-fila="<?php echo 'fila_'.$fila; ?>" class="<?php echo $fila.'-'.$columna_tasa.' cuadro-input derecha-texto'; ?>" type="text" value="<?php if ($csi_sucursal['csi_tasa']!=0) { echo number_format($csi_sucursal['csi_tasa'],2); } ; ?>" size="2"></td>
				<td class="centrar-texto"><a class="detalle_csi_suc_tasa" data-sucursal="<?php echo $csi_sucursal['sucursal']; ?>" data-id="<?php echo $csi_sucursal['id']; ?>" href=""><span class="icon-search-plus"></span></a></td>


			</tr>

			 <?php } ?>

		</tbody> 
	</table>
</div>
<div id="tabla_csi_sucursales_detalle">
	
</div>
<script src="js/csi_sucursales_cuerpo.js"></script>