<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_GET["id"]." AND visto=0 and borrar=0";
	$res=mysqli_query($con, $SQL);
	if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}

?>

<script type="text/javascript">
			$(".boton").click(function(event) {
			document.location.href ="reserva_alta.php";
			});
</script>

<a href="noticias.php" style="text-decoration:none; background:#D8F781; color:red; padding: 7px; border-radius: 5px; margin-left: 20px;" target="_blank">Notificaciones: <?php echo $cant_res['cantidad']; ?></a>