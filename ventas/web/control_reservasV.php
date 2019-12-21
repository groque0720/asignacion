<!DOCTYPE html>
<html lang="es">
<head>
    <title>Control Reservas</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_info_p.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>


<script type="text/javascript">

	$(document).ready(function(){

			var refreshId = setInterval(refrescarTablaEstadoSala, 7000);
		$.ajaxSetup({ cache: false });

		function refrescarTablaEstadoSala() {
		id=$("#idusu").val();
		$("#noti_control_res").load('control_res_act_noti.php?id='+id, function(){});
		};

		$(".boton_b").click(function(event) {
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
			$.ajax({url:"control_reservas_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});
		});

		$(".boton_s").click(function(event) {
			ini=parseInt($("#pagina").val())+20;
			$("#pagina").val(ini);
			$.ajax({url:"control_reservas_paginas.php",cache:false,type:"POST",data:{inicio:ini},success:function(result){
	      	$("#cuerpo_asesor").html(result);

	    	}});
		});

		$(".boton_a").click(function(event) {
			ini=parseInt($("#pagina").val())-20;
			if (ini>=0) {
			$("#pagina").val(ini);
			$.ajax({url:"control_reservas_paginas.php",cache:false,type:"POST",data:{inicio:ini},success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});}else{alert("No Hay Mas Registros Anteriores.")};
		});

	});
</script>

</head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>

		<?php
			include("../funciones/func_mysql.php");
			conectar();
			mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_SESSION["id"]." AND visto=0 and borrar=0";
			$res=mysqli_query($con, $SQL);
			if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
		?>


		<section id="seccion">

			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="noti_control_res" style="width: 30%; float: left; padding-top: 15px;">
					<a href="noticias.php" style="text-decoration:none; background:#D8F781; color:red; padding: 7px; border-radius: 5px; margin-left: 20px;" target="_blank">Notificaciones: <?php echo $cant_res['cantidad']; ?></a>
				</div>
				<div id="alta_sol" style="width: 30%; float: right;">
					<input type="text" id="buscar_c" name="buscar_c" placeholder="Buscar">
					<input type="button" class="boton_b" value="Buscar" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>

			</div>

			<?php


$SQL="SELECT
reservas.idreserva,
clientes.nombre AS cliente,
reservas.idtipo,
reservas.idgrupo,
reservas.idmodelo,
reservas.compra AS compra,
reservas.detalleu AS detalleu,
usuarios.nombre AS asesor,
reservas.fecres,
reservas.enviada AS enviada,
reservas.idcliente as idcliente
FROM
reservas
Inner Join clientes ON clientes.idcliente = reservas.idcliente
Inner Join usuarios ON usuarios.idusuario = reservas.idusuario
WHERE
reservas.enviada >=  1

ORDER BY
enviada ASC, reservas.fecres DESC
LIMIT 20";
			$res=mysqli_query($con, $SQL);

			 ?>
			 <hr>

				<div class="fila flechas" style="margin: 10px;">
					<div style="float: left; width: 45%;">
						<input type="button" class="boton_a" value="&#60;&#60; Anteriores" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
					<div style="float: right; width: 45%; text-align: right; margin-right: 20px;">
						<input type="button" class="boton_s" value="Siguientes &#62;&#62;" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
				</div>
			<div id="cuerpo_asesor">


				<?php include("control_reservas_cuerpo.php"); ?>
			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
