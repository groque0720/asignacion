<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="INSERT INTO recepcion_localidades (id_provincia, localidad, activo) VALUES ( '$id_provincia','$localidad',1)";
mysqli_query($con, $SQL);

$SQL="SELECT * FROM recepcion_localidades WHERE id_provincia = ".$id_provincia." ORDER BY localidad ASC";
$res_loc=mysqli_query($con, $SQL);

echo  '<option value="0">Localidad</option>';

while ($loc=mysqli_fetch_array($res_loc)) { ?>
	<option value="<?php echo $loc['id_localidad'];?>" <?php if ($loc['localidad']==$localidad) {
		echo 'selected';} ?>><?php echo $loc['localidad']; ?></option>
<?php } ?>
 ?>