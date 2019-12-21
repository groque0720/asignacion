<?php 

include_once("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="INSERT INTO recepcion_modo_acercamiento (modo_acercamiento,activo) VALUES ( '$modo_acercamiento',1)";
mysqli_query($con, $SQL);

$SQL="SELECT * FROM recepcion_modo_acercamiento ORDER BY modo_acercamiento";
$res_loc=mysqli_query($con, $SQL);

echo  '<option value="0"></option>';

while ($loc=mysqli_fetch_array($res_loc)) { ?>
	<option value="<?php echo $loc['id_modo_acercamiento'];?>" <?php if ($loc['modo_acercamiento']==$modo_acercamiento) {
		echo 'selected';} ?>><?php echo $loc['modo_acercamiento']; ?></option>
<?php } ?>
 ?>