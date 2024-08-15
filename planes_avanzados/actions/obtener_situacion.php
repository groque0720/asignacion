<?php 
    $SQL="SELECT id, situacion FROM tpa_plan_situaciones WHERE activo = 1 ORDER BY situacion";
	$situaciones=mysqli_query($con, $SQL);
?>