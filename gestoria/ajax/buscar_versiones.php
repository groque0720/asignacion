<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

$SQL="SELECT * FROM modelos WHERE  activo = 1 AND idgrupo = ".$modelo;
$res_modelos = mysqli_query($con, $SQL);
echo "<option value=''>Seleccionar Versión</option>";
while ($mod=mysqli_fetch_array($res_modelos)) {?>
	
	<option value="<?php echo $mod['idmodelo']; ?>">
		<?php echo $mod['modelo']; ?>
	</option>
<?php } ?>