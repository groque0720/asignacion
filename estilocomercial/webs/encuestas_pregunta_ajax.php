<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	$opcion=$_GET["opcion"];
	$elegido=$_GET["elegido"];

	//-----------------cambio de formato

	if ($opcion=="cambio_formato") {
		$SQL="SELECT * FROM encuestas_tipos_respuestas WHERE id_formato_respuesta='".$elegido."'";
		$form=mysqli_query($con, $SQL);
	?>

	<option value="0" selected></option>
	<?php  while ($tipo_respuesta=mysqli_fetch_array($form)) { ?>
		<option value="<?php echo $tipo_respuesta["id_tipo_respuesta"] ?>"><?php echo $tipo_respuesta["tipo_respuesta"]; ?></option>
	<?php }  //cierre del while

	}//cierre del if?>

	<?php

	//-----------------cambio de tipo

	if ($opcion=="cambio_tipo_respuesta") {
	$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE id_tipo_respuesta='".$elegido."'";
	$form=mysqli_query($con, $SQL);
	 ?>

	<option value="0" selected>#S/O</option>
	<?php  while ($linea_respuestas=mysqli_fetch_array($form)) { ?>
		<option value="<?php echo $linea_respuestas["id_linea_tipo_respuesta"] ?>"><?php echo $linea_respuestas["linea_tipo_respuesta"]; ?></option>
	<?php }

	} ?>





?>



<?php mysqli_close($con); ?>