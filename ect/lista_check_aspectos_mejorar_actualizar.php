
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);


	if ($valor==1) {
		$SQL="INSERT INTO ect_pmi_asesores_aspectos_mejorar (id_asesor_ect, id_mes, ano, id_item, observacion) VALUES ( $id_asesor, $mes, $ano, $id_item, '".$obs."')";
	};

	if ($valor==0){
		$SQL="DELETE FROM ect_pmi_asesores_aspectos_mejorar WHERE id_asesor_ect = $id_asesor AND id_mes = $mes AND ano = $ano AND id_item = $id_item";
	};

	if ($valor==3) {
		$SQL="UPDATE ect_pmi_asesores_aspectos_mejorar SET observacion = '".$obs."' WHERE id_asesor_ect = $id_asesor AND id_mes = $mes AND ano = $ano AND id_item = $id_item";
	};

	mysqli_query($con, $SQL);

	echo $SQL;


 ?>
