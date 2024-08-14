<?php 
    $SQL="SELECT id, estado FROM tpa_planes_avanzados_estados WHERE activo = 1 ORDER BY estado";
	$estados=mysqli_query($con, $SQL);
?>