<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
 ?>


<option value="0"></option>
<?php 
$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo = ".$id_modelo." ORDER BY posicion";
$versiones=mysqli_query($con, $SQL);
while ($version=mysqli_fetch_array($versiones)) { ?>
	<option value="<?php echo $version['idmodelo']; ?>"><?php echo $version['modelo']; ?></option>
<?php }	?>