<?php 
    $SQL="SELECT id, modalidad FROM tpa_modalidades WHERE activo = 1 ORDER BY modalidad";
	$modalidades=mysqli_query($con, $SQL);
?>