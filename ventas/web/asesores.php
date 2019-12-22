<!DOCTYPE html>
<html lang="es">
<head>
    <title>Operaciones Activas</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="500">
   <link rel="shortcut icon" type="image/x-icon" href="../dyv.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_info_p.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

    <script type="text/javascript">

$(document).ready(function(){


	//var refreshId = setInterval(refrescarTablaEstadoSala, 10000);
	//$.ajaxSetup({ cache: false });

	//function refrescarTablaEstadoSala() {
	//id=$("#idusu").val();
	//$("#alta_sol").load('asesores_act_noti.php?id='+id, function(){});
	//};

	$("#dialog").dialog({
		autoOpen: false,
		width: 600,
		height: 300,
		modal: true,
		buttons: {
			"Cerrar": function () {
			$(this).dialog("close");
				}
			}
		});

		$(".boton").click(function(event) {
			document.location.href ="reserva_alta.php";
			});


		$(".boton_b").click(function(event) {
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
		    $.ajax({url:"asesores_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
      		$("#cuerpo_asesor").html(result);
    		}});

			});

		$(".anular_reserva").click(function(event) {
			if (confirm("Seguro que deseas anular la operaci\u00f3n??")) {
				var id = $(this).attr('data-id'); //llamar a ajax anular la operacion y volver a la pagina asesores
				var obs = prompt("Ingrese Motivo por la cual anula la reserva.");


				if (obs!="" && obs != null) {
					document.location.href = "reserva_anular.php?idres=" + id + "&obser=" + obs + "&";
					//return false;
				};
			};
		});

		$(".boton_s").click(function(event) {
			ini=parseInt($("#pagina").val())+15;
			$("#pagina").val(ini);
			idusuario =$("#idusu").val();
			$.ajax({url:"asesores_paginas.php",cache:false,type:"POST",data:{inicio:ini, idusu:idusuario},success:function(result){
	      	$("#cuerpo_asesor").html(result);

	    	}});
		});

		$(".boton_a").click(function(event) {
			ini=parseInt($("#pagina").val())-15;
			if (ini>=0) {
			$("#pagina").val(ini);
			idusuario =$("#idusu").val();
			$.ajax({url:"asesores_paginas.php",cache:false,type:"POST",data:{inicio:ini, idusu:idusuario},success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});}else{alert("No Hay Mas Registros Anteriores.")};
		});

		$(".facturar").click(function(event) {

		var id = $(this).attr('data-id');
			$.ajax({
				url:"facturacion_cargar.php",
				cache:false,
				type:"POST",
				data:{idres:id},
				success:function(result){
					document.location.href ="facturacion.php?IDrecord="+id;
				}
			})
		});

	});
   </script>

</head>

<body>

<div id="agrupar">

		<?php include("../includes/header.php") ?>

		<section id="seccion">


			<?php
				include("../funciones/func_mysql.php");
				conectar();
				////mysql_query("SET NAMES 'utf8'");
				//$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_SESSION["id"]." AND visto=0 and borrar=0";
				//$res=mysqli_query($con, $SQL);
				//if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}

			?>


			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<?php
					$SQL="SELECT id_usuario_dos FROM usuarios WHERE idusuario = ".$_SESSION["id"];
					$res_usu_dos = mysqli_query($con, $SQL);
					$usu_dos=mysqli_fetch_array($res_usu_dos);
					$dos_usu= $usu_dos["id_usuario_dos"];

				 ?>
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="alta_sol" style="width: 32%; float: left;">
					<input type="button" class="boton" value="Nueva Reserva" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
					<!--<a href="noticias.php" style="text-decoration:none; background:#D8F781; color:red; padding: 7px; border-radius: 5px; margin-left: 20px;" target="_blank">Notificaciones: <?php // echo $cant_res['cantidad']; ?></a>-->

				</div>


				<div id="alta_sol" style="width: 29%; float: right;">
					<input type="text" id="buscar_c" name="buscar_c" placeholder="Buscar">
					<input type="button" class="boton_b" value="Buscar" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>

				</div>

			</div>

			<?php

			$SQL="SELECT * FROM reservas WHERE idusuario =".$_SESSION["id"]." AND not isnull(compra)  AND entregada < 3  ORDER BY fecres DESC, idreserva DESC LIMIT 15";
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
				<?php include("asesor_cuerpo.php") ?>
			</div>
		</section>

	</div>


</body>
<?php mysqli_close($con); ?>

</html>
