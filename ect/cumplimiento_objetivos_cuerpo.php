
<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);


$SQL="SELECT * FROM ect_tipos_objetivos WHERE activo = 1";
$objetivos=mysqli_query($con, $SQL);
$cant_objetivos = mysqli_num_rows($objetivos);

if ($cant_objetivos>1) {
	$i=0;
	while ($objetivo=mysqli_fetch_array($objetivos)) {
		$objetivos_array[$i]["id"]=$objetivo["id"];
		
		$i++;
	}
}

$SQL="SELECT * FROM ect_modelos WHERE activo = 1 AND borrar=0";
$modelos=mysqli_query($con, $SQL);
$cant_modelos = mysqli_num_rows($modelos);

if ($cant_modelos>1) {
	$i=0;
	while ($modelo=mysqli_fetch_array($modelos)) {
		$modelos_array[$i]["id"]=$modelo["id"];
		$modelos_array[$i]["modelo"]=$modelo["modelo"];
		$i++;
	}

}

$SQL="SELECT * FROM ect_view_asesores_activos";
$asesores=mysqli_query($con, $SQL);

	while ($asesor= mysqli_fetch_array($asesores)) {

		for ($i=0; $i < $cant_objetivos; $i++) { 

			for ($j=0; $j < $cant_modelos; $j++) { 

				$SQL="SELECT id FROM ect_objetivos_cumplimiento WHERE id_asesor=".$asesor['id_asesor_ect']." AND id_mes = {$mes} AND ano = {$ano} and id_tipo_objetivo = ".$objetivos_array[$i]['id']." AND id_modelo=".$modelos_array[$j]['id'];
				$obj_cumplimientos = mysqli_query($con, $SQL);

				$cant = mysqli_num_rows($obj_cumplimientos);

				if ($cant == 0) {
					
					$SQL="INSERT INTO ect_objetivos_cumplimiento (id_mes, ano, id_asesor, id_tipo_objetivo, id_modelo) VALUES ( {$mes} , {$ano}, ".$asesor['id_asesor_ect'].", ". $objetivos_array[$i]['id'].", ". $modelos_array[$j]['id'].")";
					mysqli_query($con, $SQL);
				}
				
			}

		}
	
	}
?>

<table class="ancho-80 margen-arriba-10">
	<colgroup>
			<col width="5%"> 
			<col width="5%">
			<?php 
				for ($i=0; $i < $cant_modelos; $i++) { ?>
					<col width="3%">
			<?php } ?>
			<!-- <col width="2%"> -->

	</colgroup>
	<thead>
		<tr>
			<td>Sucursal</td>
			<td>Asesor</td>
			<?php 
				for ($i=0; $i < $cant_modelos; $i++) { ?>
					<td><?php echo $modelos_array[$i]["modelo"]; ?></td>
			<?php } ?>
			<!-- <td><span class="icon-delete"></span></td> -->
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

			$SQL="SELECT * FROM ect_view_asesores_activos";
			$asesores=mysqli_query($con, $SQL);

		$fila=0;
		while ($asesor = mysqli_fetch_array($asesores)) { $fila++;?>

		<tr class="<?php echo 'filas fila_'.$fila; ?>">
			<td class="centrar-texto" ><?php echo $sucursal_a[$asesor["id_sucursal"]]['sucursal']; ?></td>
			<td class="centrar-texto" ><?php echo $asesor['asesor']; ?></td>
			<?php 
				for ($i=0; $i < $cant_modelos; $i++) { 

					$SQL="SELECT * FROM ect_objetivos_cumplimiento WHERE id_mes= $mes AND ano = $ano AND id_tipo_objetivo = $id_tipo_objetivo AND id_asesor = ".$asesor['id_asesor_ect']." AND id_modelo =".$modelos_array[$i]['id'];
					$objetivos=mysqli_query($con, $SQL);

					$objetivo= mysqli_fetch_array($objetivos);

					$columna_celda = $i+3;

					?>
					<td class="centrar-texto"> <input data-id="<?php echo $objetivo['id']; ?>" data-nrofila="<?php echo $fila; ?>"  data-columna="<?php echo $i+3; ?>" data-fila="<?php echo 'fila_'.$fila; ?>" class="<?php echo $fila.'-'.$columna_celda.' cuadro-input derecha-texto'; ?>" type="text" value="<?php if ($objetivo['cumple']!=0) { echo $objetivo['cumple']; } ; ?>" size="2"></td>
			<?php } ?>
			<!-- <td class="centrar-texto" ><span data-fila="<?php echo 'fila_'.$fila; ?>" data-id="<?php echo $objetivo['id']; ?>" class="icon-delete borrar-activacion"></span></td>	 -->			

		</tr>

		 <?php } ?>

	</tbody> 
	</table>

<input type="hidden" id="cantidad_filas" value="<?php echo $fila; ?>">
<script src="js/cumplimiento_objetivos_cuerpo.js"></script>