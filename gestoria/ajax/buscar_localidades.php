<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

$SQL="SELECT * FROM registros_gestoria_localidades WHERE id_provincia = ".$prov." ORDER BY localidad ASC";
$res_loc=mysqli_query($con, $SQL);

echo '<option value="1">Selec. Localidad</option>';
//echo '<option value="12">Selec. Localidad</option>';									
while ($loc=mysqli_fetch_array($res_loc)) { ?>
<option value="<?php echo $loc['id_localidad'];?>"><?php echo $loc['localidad']; ?></option>
<?php } ?>