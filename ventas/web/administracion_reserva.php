<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title></title>
    <link rel="shortcut icon" type="image/x-icon" href="../dyv.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo_sol.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_sol_p.css">
  <link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--     // <script language="javascript" src="../js/jquery-1.10.2.js"></script> -->

    <script language="javascript">

	$(document).ready(function(){

		$('select').attr('disabled', true);

		$("#ingreso_codigo").hide(0)

		$("table tr td:last-child").css({
   				"display": "none"
				})

		$("form").keypress(function(e) {
        if (e.which == 13) {
        	return false;
        }
    	});



	if ("Nuevo"==$("#compra").val()) {
		$("#nuevo").show(0);
	  	$("#usado").hide(0);
	  	$("#titulo").html('SOLICITUD DE RESERVA DE VEHICULO OKM');
	}else {
			$("#nuevo").hide(0);
			$("#usado").show(0);
			$("#titulo").html('SOLICITUD DE RESERVA DE VEHICULO USADO');
				};


  if ("Reemplazo"==$("#tipocompra").val()) {
		$(".mr").show(250);
	  	}else {
		$(".mr").hide(250);
		};

 	 })

  	</script>

  	<style type="text/css">
	#act_ajax table {
		width: 100%;
	}

	#act_ajax table thead {
		background: #ccc;
		text-align: center;
	}

	#act_ajax table tr{
		height: 22px;
	}
	#act_ajax {
		font-size: 1em;
	}
 </style>



</head>

<body>

	<div id="agrupar">


		<?php
		include("../funciones/func_mysql.php");
		conectar();
		//mysql_query("SET NAMES 'utf8'");
		$SQL="SELECT * FROM tipos";
		$tipos=mysqli_query($con, $SQL);

		$SQL="SELECT * FROM reservas WHERE idreserva=".$_GET["IDrecord"];
		$res=mysqli_query($con, $SQL);
		$reserva=mysqli_fetch_array($res);

		$SQL="SELECT * FROM clientes WHERE idcliente=".$reserva['idcliente'];
		$cli=mysqli_query($con, $SQL);
		$clientes=mysqli_fetch_array($cli);

		$SQL="SELECT * FROM entregausado WHERE identregau=".$reserva['identregau'];
		$usue=mysqli_query($con, $SQL);
		$usadoe=mysqli_fetch_array($usue);

		$SQL="SELECT * FROM usuarios WHERE idusuario=".$reserva['idusuario'];
		$usuarios=mysqli_query($con, $SQL);
		$usuario=mysqli_fetch_array($usuarios);


 		?>

		<?php include("../includes/header.php") ?>
		<div id="atras" style="margin: 10px;">
			<a href="javascript:window.history.back();">&laquo; Volver atrás</a>
			<!-- <input type="button" class="boton" value="<- Volver" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/> -->
		</div>

		<section id="seccion">



			<div id="solicitud">

				<form id="form_solicitud" name="form_solicitud" action="reserva_edit.php" method="POST">

					<input id="nrores" name="nrores" type="hidden" value="<?php echo $_GET["IDrecord"]; ?>" >
					<input id="idcliente" name="idcliente" type="hidden" value="<?php echo $reserva['idcliente']; ?>" >
					<input id="identregau" name="identregau" type="hidden" value="<?php echo $reserva['identregau']; ?>" >
					<input id="enviado" name="enviado" type="hidden" value="<?php if ($reserva['enviada']==1) { echo 1; }else { echo 0; } ?>" >



					<?php include("../includes/solicitud/cabecera.php"); ?>

					<?php include("../includes/solicitud/unidad.php"); ?>


					<fieldset>



						<div id="act_ajax">

						<?php include("../includes/solicitud/detalle.php") ?>

						</div>

						</fieldset>

					<?php include("../includes/solicitud/cliente.php") ?>


					<?php include("../includes/solicitud/encuesta.php") ?>

					<?php include("../includes/solicitud/usado.php") ?>

					<?php include("../includes/solicitud/observaciones.php") ?>

					<?php include("../includes/solicitud/legal.php") ?>

					<?php include("../includes/solicitud/firma.php") ?>


					<div id="btn_gdr">


					</div>

				</form>

			</div>

		</section>


	</div>


</body>
</html>
