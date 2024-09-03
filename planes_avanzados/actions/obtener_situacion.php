<?php 
    $SQL="SELECT id, situacion FROM tpa_plan_situaciones WHERE activo = 1 ORDER BY orden ASC";
	$situaciones=mysqli_query($con, $SQL);
?>