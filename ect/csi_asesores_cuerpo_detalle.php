
<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);



$SQL="SELECT * FROM ect_csi_item_dyv WHERE activo = 1 ORDER BY posicion";
$items=mysqli_query($con, $SQL);


while ($item=mysqli_fetch_array($items)) {
	
	$SQL="SELECT id FROM ect_csi_asesores_dyv_detalle WHERE id_csi_asesor= $id AND id_item_dyv = ".$item['id'];
	$csi_detalles=mysqli_query($con, $SQL);

	$cant=mysqli_num_rows($csi_detalles);

	if ($cant == 0 ) {
			
			$SQL="INSERT INTO ect_csi_asesores_dyv_detalle (id_csi_asesor, id_item_dyv) VALUES ( $id,".$item['id'].")";
			mysqli_query($con, $SQL);
		}

}



?>

<div class="ancho-50 negrita centrar-caja">
	<a href="" id="volver_listado_gral"><span class="icon-outdent">Volver al listado General</span></a>
</div>
<div class="ancho-50 centrar-texto centrar-caja">
	<?php echo $asesor; ?>
</div>

<table class="ancho-50 margen-arriba-10">
	<colgroup>
			<col width="85%"> 
			<col width="15%">
	</colgroup>
	<thead>
		<tr>
			<td>Item</td>
			<td>Puntaje</td>
		</tr>
	</thead>

	<?php 

		$SQL="SELECT * FROM ect_csi_asesores_dyv_detalle WHERE id_csi_asesor= $id";
		$csi_detalles=mysqli_query($con, $SQL);
		$fila=0;
		while ($csi_detalle = mysqli_fetch_array($csi_detalles)) { $fila++; $columna_celda=2;

			$SQL="SELECT * FROM ect_csi_item_dyv WHERE id = ".$csi_detalle['id_item_dyv'];
			$items=mysqli_query($con, $SQL);
			$item=mysqli_fetch_array($items);

			?>
			<tr>
				<td><?php echo $item['detalle']; ?></td>
				<td class="centrar-texto">
				<input data-id="<?php echo $csi_detalle['id']; ?>" data-nrofila="<?php echo $fila; ?>"  data-columna="<?php echo $columna_celda ?>" data-fila="<?php echo 'fila_'.$fila; ?>" class="<?php echo $fila.'-'.$columna_celda.' cuadro-input derecha-texto'; ?>" type="text" value="<?php echo $csi_detalle['puntaje']; ?>" size="2">
				</td>

			</tr>
		<?php } ?>




	</tbody> 
	</table>

<script src="js/csi_asesores_cuerpo_detalle.js"></script>