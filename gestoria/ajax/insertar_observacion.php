<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

$SQL="INSERT INTO registros_gestoria_obs (id_reg_gestoria, fecha, obs) VALUES ( '$id_reg','$fecha','$texto')";
mysqli_query($con, $SQL);


$SQL = "SELECT * FROM registros_gestoria_obs WHERE id_reg_gestoria = ".$id_reg." ORDER BY fecha DESC";
$res_obs=mysqli_query($con, $SQL);

?>

<table>
	<thead>
		<tr>
			<td width="15%">Fecha</td>
			<td>Observación</td>
		</tr>
	</thead>
	<tbody>
		<?php 
		while ($obs=mysqli_fetch_array($res_obs)) {?>
			<tr>
				<td class="centrar-texto"><?php echo cambiarFormatoFecha($obs['fecha']); ?></td>
				<td><?php echo $obs['obs']; ?></td>
			</tr>
		
		 <?php }?>
	</tbody>
</table>	