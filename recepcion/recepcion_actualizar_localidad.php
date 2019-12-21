<?php
 
include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="SELECT * FROM recepcion_localidades WHERE id_provincia = ".$_POST['id_provincia'];
$localidades = mysqli_query($con, $SQL);

echo "<option value='0'></option>";

while ($localidad=mysqli_fetch_array($localidades)) { ?>
	<option value="<?php echo $localidad['id_localidad']?>" ><?php echo $localidad['localidad'] ?></option>
<?php } ?>
