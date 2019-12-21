<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo = ".$_POST['id_grupo'];
$versiones = mysqli_query($con, $SQL);

echo "<option value='0'></option>";

while ($version=mysqli_fetch_array($versiones)) { ?>
	<option value="<?php echo $version['idmodelo']; ?>"><?php echo $version['modelo']; ?></option>
<?php }	?>