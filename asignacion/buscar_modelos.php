<?php
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
 ?>


<option value="0"></option>
<?php
$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo = ".$grupo." ORDER BY posicion";
$versiones=mysqli_query($con, $SQL);
while ($version=mysqli_fetch_array($versiones)) { ?>
	<option value="<?php echo $version['idmodelo']; ?>" <?php if ($version['idmodelo']==$unidad['id_modelo']) { echo 'selected'; } ?>><?php echo $version['modelo']; ?></option>
<?php }	?>