<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT MAX(id_act) as id_act FROM a_modificaciones WHERE modelo_activo = $modelo_activo";
	$modificaciones=mysqli_query($con, $SQL);
	$cant=mysqli_num_rows($modificaciones);

	if ($cant>0) {

		$result=mysqli_fetch_array($modificaciones);

		if ((int)$result['id_act']>(int)$nro_act) {
			echo $result['id_act'];
		}else{
			echo "0";
		}

	}else{
		echo "0";
	}

		
?>


