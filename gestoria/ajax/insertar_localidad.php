<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

$SQL="INSERT INTO registros_gestoria_localidades (id_provincia, localidad) VALUES ( '$prov','$nva_loc')";
mysqli_query($con, $SQL);

$SQL="SELECT * FROM registros_gestoria_localidades WHERE id_provincia = ".$prov." ORDER BY localidad ASC";
$res_loc=mysqli_query($con, $SQL);

echo  '<option value="0">Localidad</option>';

while ($loc=mysqli_fetch_array($res_loc)) { ?>
	<option value="<?php echo $loc['id_localidad'];?>" <?php if ($loc['localidad']==$nva_loc) {
		echo 'selected';} ?>><?php echo $loc['localidad']; ?></option>
<?php } ?>