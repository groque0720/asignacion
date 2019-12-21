<?php
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	$formato=$_POST["formato"];
	$id=$_POST["id"];
	$nro=$_POST["nro"];
	$id_cuestionario = $_POST["id_cuestionario"];
	$id_estado=$_POST["id_estado"];

	if ($formato==3) {
			$texto=$_POST["texto"];
			$SQL="UPDATE cuestionarios_respuestas SET ";
			$SQL .=" observacion = '".$texto."'";
			$SQL .=" WHERE id_respuesta_cuestionario = ".$nro;
			mysqli_query($con, $SQL);
	}else{

		$valor=$_POST["valor"];

		if ($formato!=2) {
			$SQL="UPDATE cuestionarios_respuestas_lineas SET ";
			$SQL .=" respuesta = 0 ";
			$SQL .=" WHERE id_respuesta_cuestionario = ".$nro;
			mysqli_query($con, $SQL);
		}

		$SQL="UPDATE cuestionarios_respuestas_lineas SET ";
		$SQL .=" respuesta = ".$valor;
		$SQL .=" WHERE id_cuestionario_respuestas_lineas = ".$id;
		mysqli_query($con, $SQL);
	};

	if ($id_estado>=1) {
		$SQL="UPDATE cuestionarios SET id_estado_cuestionario = 2 WHERE id_cuestionario = '".$id_cuestionario."'";
		mysqli_query($con, $SQL);
	};

		$SQL="SELECT * FROM cuestionarios_estados WHERE activo = 1";
		$form=mysqli_query($con, $SQL);

		?>

		<select name="id_estado_cuestionario" id="id_estado_cuestionario" required>
			<option value="0"></option>
			<?php  while ($formato=mysqli_fetch_array($form)) { ?>
				<option value="<?php echo $formato["id_estado_cuestionario"]; ?>" <?php if ($formato["id_estado_cuestionario"]==2) { echo "selected";}?>><?php echo $formato["estado_cuestionario"]; ?></option>
			<?php } ?>
		</select>

		<?php






	mysqli_close($con);
 ?>