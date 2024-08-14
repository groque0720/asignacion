<?php 
    $SQL="SELECT id, modelo FROM tpa_modelos WHERE activo = 1 ORDER BY modelo";
	$modelos=mysqli_query($con, $SQL);
?>