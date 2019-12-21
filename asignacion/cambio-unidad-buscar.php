<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

$SQL="SELECT cliente FROM asignaciones WHERE nro_unidad = ".$id_unidad;
$res=mysqli_query($con, $SQL);
$cant=mysqli_num_rows($res);

if ($cant!=0) {
	$unidad=mysqli_fetch_array($res);
	if ($unidad['cliente']!='') {?>
		<input class="form-inputs" type="text" size="30" id="cliente_uno" name="cliente_uno" value="<?php echo $unidad['cliente']; ?>" readonly="readonly">
	<?php }else{?>
		<input class="form-inputs inputs-sin-cliente" type="text" size="30" id="cliente_uno" name="cliente_uno" value="<?php echo 'Sin Cliente'; ?>" readonly="readonly">
	<?php }?>
	
<?php }else{ ?>
	<input class="form-inputs inputs-no-cliente" type="text" size="30" id="cliente_uno" name="cliente_uno" value="NO EXISTE" readonly="readonly">
	<input type="hidden" size="30" class="bandera" value="1" readonly="readonly">
<?php } ?>




