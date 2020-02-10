<!DOCTYPE html>
<html lang="es">
<head>
	<title></title>
	<meta charset="UTF-8">
	 <meta http-equiv="refresh" content="500">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="stylesheet" href="../css/styles.css">
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="../css/jquery-ui.css"></script>
	<style>
		.ui-dialog-titlebar{
			display: none;
		}
		#form_excel {
			border: 1px solid #000;
		}
		.item {
			border: 1px solid #D2CACA;
			border-radius: 5px;
			margin: 12px;
			padding: 10px;
			transition:all 0.3s ease;
			position: relative;
		}
		.cantidad {
			position: absolute;
			padding: 5px 10px;
			right: -5px;
			background: red;
			color: white;
			font-weight: bold;
			border-radius: 50%;
			top: -5px;
		}

		.item:hover .titulo{
			color:red;
			font-weight: bold;
		}

		.item:hover {
			box-shadow: 3px 3px 2px #D8D1D1;
		}


	</style>
	<script>
		$(document).ready(function(){

		})
	</script>
</head>
<body>
<?php
include("../includes/security.php");
include("../funciones/func_mysql.php");
conectar();
@session_start();
// //mysql_query("SET NAMES 'utf8'");
?>
	<div class="ed-container">
		-
	</div>
	<div class="ed-container web-60">
		<div class="ed-item web-1-3 movil-1-3">
			<img class="web-40 movil-30" src="../imagenes/logodyv.png" alt="logodyv">
		</div>
		<div class="ed-item web-1-3 movil-1-3 centrar-texto">
			<span>Panel de Notificaciones</span>
		</div>
		<div class="ed-item web-1-3 movil-1-3 derecha-contenido ">
			<img class="web-40 movil-25" src="../imagenes/logo_toyota.png" alt="logodyv">
		</div>
		<div class="ed_item total">
		<hr>
		</div>
	</div>
	<div class="ed-container web-60">

		<div class="ed-item web-1-6 item">
			<div class="img">
				<a href="../../asignacion"  target="_blank">
					<div class="ed-container">
						<div class="ed-item">
							<img src="../imagenes/asignacion.png" alt="">
						</div>
					</div>
					<div class="centrar-texto">
						<span class="titulo">
							Planilla Asignación
						</span>
					</div>
				</a>
			</div>

		</div>





		<div class="ed-item web-1-6 item">
			<div class="img">
			<a href="asesores.php" target="_blank">
				<div class="ed-container">
					<div class="ed-item">
						<img src="../imagenes/reservas.png" alt="">
					</div>
				</div>
				<div class="centrar-texto">
					<span class="titulo">
						Operaciones
					</span>
				</div>
			</a>
			</div>

		</div>

		<?php if ($_SESSION["id"]==94 OR $_SESSION["id"]==96) { ?>

			<div class="ed-item web-1-6 item">
				<div class="img">
				<a href="pagos_clientes.php">
					<div class="ed-container">
						<div class="ed-item">
							<img src="../imagenes/pagos.png" alt="">
						</div>
					</div>
					<div class="centrar-texto">
						<span class="titulo">
							Control de Pagos
						</span>
					</div>
				</a>
				</div>
			</div>

			<div class="ed-item web-1-6 item">
				<div class="img">
				<a href="/gestoria">
					<div class="ed-container">
						<div class="ed-item">
							<img src="../../dashboard/imagenes/gestoria.png" alt="">
						</div>
					</div>
					<div class="centrar-texto">
						<span class="titulo">
							Gestoría
						</span>
					</div>
				</a>
				</div>
			</div>



			<div class="ed-item web-1-6 item">
				<div class="img">
				<a href="/ect">
					<div class="ed-container">
						<div class="ed-item">
							<img src="../../dashboard/imagenes/confencuestas.jpg" alt="">
						</div>
					</div>
					<div class="centrar-texto">
						<span class="titulo">
							Estilo Comercial
						</span>
					</div>
				</a>
				</div>
			</div>



		<?php } ?>




		<?php if ($_SESSION["id"]==43) { ?>

			<div class="ed-item web-1-6 item">
				<div class="img">
				<a href="/ect">
					<div class="ed-container">
						<div class="ed-item">
							<img src="../../dashboard/imagenes/confencuestas.jpg" alt="">
						</div>
					</div>
					<div class="centrar-texto">
						<span class="titulo">
							Estilo Comercial
						</span>
					</div>
				</a>
				</div>
			</div>

		<?php } ?>


		<div class="ed-item web-1-6 item">
<!-- 			<?php
				$SQL="SELECT count(visto) as cantidad FROM recepcion WHERE visto = 0 AND id_asesor = ".$_SESSION["id"];
				$res_cantidad = mysqli_query($con, $SQL);
				$noti = mysqli_fetch_array($res_cantidad);

				if ($noti['cantidad']!=0) { ?>
					<div class="cantidad">
						<span><?php echo $noti['cantidad']; ?></span>
					</div>
				<?php } ?> -->
			<div class="img">

			<?php if ($_SESSION["idsuc"]==3) { ?>
				<a href="https://www.toyotacfa.com.ar/simulador-creditos" target="_blank">
			<?php }else{ ?>
			<a href="https://www.tcfautos.com.ar/#/cotizador/" target="_blank">
			<?php } ?>

				<div class="ed-container">
					<div class="ed-item">
						<img src="../imagenes/tcfa.png" alt="">
					</div>
				</div>
				<div class="centrar-texto">
					<span class="titulo">
						Simulación Crédito TCFA
					</span>
				</div>
			</a>
			</div>
		</div>

		<div class="ed-item web-1-6 item">
			<div class="img">
			<a href="http://crmdyv.online" target="_blank">
				<div class="ed-container">
					<div class="ed-item">
						<img src="../imagenes/crm.jpg" alt="">
					</div>
				</div>
				<div class="centrar-texto">
					<span class="titulo">
						CRM
					</span>
				</div>
			</a>
			</div>
		</div>














		<div class="ed-item web-1-6 item">
			<?php
				$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_SESSION["id"]." AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}

				if ($cant_res['cantidad']!=0) { ?>
					<div class="cantidad">
						<span><?php echo $cant_res['cantidad']; ?></span>
					</div>
			<?php } ?>
			<div class="img">
			<a href="noticias.php">
				<div class="ed-container">
					<div class="ed-item">
						<img src="../imagenes/notificaciones.png" alt="">
					</div>
				</div>
				<div class="centrar-texto">
					<span class="titulo">
						Notificaciones Reservas
					</span>
				</div>
			</a>
			</div>
		</div>
		<div class="ed-item web-1-6 item">
			<div class="img">
			<a href="../../agenda_test_drive">
				<div class="ed-container">
					<div class="ed-item">
						<img src="../imagenes/agenda_td.png" alt="">
					</div>
				</div>
				<div class="centrar-texto">
					<span class="titulo">
						Agenda Test Drive
					</span>
				</div>
			</a>
			</div>
		</div>


		<div class="ed-item web-1-6 item">
			<?php
				$SQL="SELECT count(visto) as cantidad FROM recepcion WHERE visto = 0 AND id_asesor = ".$_SESSION["id"];
				$res_cantidad = mysqli_query($con, $SQL);
				$noti = mysqli_fetch_array($res_cantidad);

				if ($noti['cantidad']!=0) { ?>
					<div class="cantidad">
						<span><?php echo $noti['cantidad']; ?></span>
					</div>
				<?php } ?>
			<div class="img">
			<?php if ($_SESSION["idsuc"]==3) { ?>
				<a href="../../recepcion" target="_blank">
			<?php }else{ ?>
			<a href="../../recepcion/recepcion_asesores.php" target="_blank">
			<?php } ?>
				<div class="ed-container">
					<div class="ed-item">
						<img src="../imagenes/contacto_rc.png" alt="">
					</div>
				</div>
				<div class="centrar-texto">
					<span class="titulo">
						Contactos Recepción
					</span>
				</div>
			</a>
			</div>
		</div>




		<?php

			$SQL="SELECT * FROM publicaciones_temas WHERE activo = 1";
			$res_temas=mysqli_query($con, $SQL);

			$usu = $_SESSION["id"];

			while ($tema = mysqli_fetch_array($res_temas)) { $tema_sel=$tema['id_publicacion_tema'];?>

				<div class="ed-item web-1-6 item">

					<?php
						$SQL="SELECT count(visto) as cantidad FROM publicaciones_linea WHERE id_tema = $tema_sel AND visto = 0 AND idusuario = $usu";
						$res_cantidad = mysqli_query($con, $SQL);
						$noti = mysqli_fetch_array($res_cantidad);

						if ($noti['cantidad']!=0) { ?>
							<div class="cantidad">
								<span><?php echo $noti['cantidad']; ?></span>
							</div>
						<?php } ?>

					<div class="img">
						<a href="<?php echo "notificaciones_lista_asesores.php?id_tema=$tema_sel&id=$usu"; ?>">
							<div class="ed-container">
								<div class="ed-item">
									<img src="<?php echo $tema['imagen']; ?>" alt="">
								</div>
							</div>
							<div class="centrar-texto">
								<span class="titulo">
									<?php echo $tema['tema']; ?>
								</span>
							</div>
						</a>
					</div>
				</div>


			<?php } ?>

	</div>


</body>
</html>