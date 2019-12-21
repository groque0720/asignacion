
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	if ($que_csi == 'dyv') {
		$SQL="UPDATE ect_csi_sucursales SET csi_dyv =  {$valor}  WHERE id =  {$id} ";
		mysqli_query($con, $SQL);
	}else{
		$SQL="UPDATE ect_csi_sucursales SET csi_tasa =  {$valor}  WHERE id =  {$id} ";
		mysqli_query($con, $SQL);		
	}


 ?>


