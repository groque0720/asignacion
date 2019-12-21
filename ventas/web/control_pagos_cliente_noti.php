<?php
	include("../funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_GET["id"]." AND visto=0 and borrar=0";
	$res=mysqli_query($con, $SQL);
	if (empty($res)) {$cant_res['cantidad']=0;}else{
		 $cant_res=mysqli_fetch_array($res);
		if ($cant_res['cantidad']>0) { ?>
		<a href="noticias.php" style="text-decoration:none;color:white;"  target="_blank">Notificaciones Nuevas:
		<?php echo $cant_res['cantidad'];}
	}
		mysqli_close($con);
?>
	</a>