<?php
include ("../includes/security.php");?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title>Noticias - DYV S. A.</title>
	<link rel="stylesheet" href="../css/notificaciones.css" />

     <link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>
	<!-- C:\xampp\htdocs\xampp\www\solicitud\css -->

	<script type="text/javascript">
	$(document).ready(function(){

		ion.sound({
	        sounds: [
	            {name: "door_bell"},
	            {name: "bell_ring"}
	        ],
	        path: "../sonidos/",
	        preload: true,
	        volume: 1.0
	    });

    // pagar este codigo cuando quiero sonido ->

		var refreshId = setInterval(refrescarTablaEstadoSala, 10000);
		$.ajaxSetup({ cache: false });

		function refrescarTablaEstadoSala() {
			$("#titulo").load('noti_barra.php', function(){});

			if ($("#numero_anterior").val()!= $("#numero_act").val()) {
				ion.sound.play("door_bell");
				$("#numero_anterior").val(parseInt($("#numero_act").val()));
			};
		};

		$("#img_carga").hide();

	});

	</script>


</head>
<body>

	<div id="agrupar" class="agrupar">

		<input id="idusuario" name="idusuario" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
		<input id="pos_not" name="pos_not" type="hidden" value="">

		<div id="titulo" class="titulo">
			<?php include("noti_barra.php") ?>
		</div>

			<?php
				$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_SESSION["id"]." AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
			?>
			 <input type="hidden" id="numero_anterior" value="<?php echo $cant_res['cantidad']; ?>">

		<div class="zona_tabla" id="zona_tabla">

			<div id="leyenda" class="leyenda">
					<div class="bienvenida">
						Elija una Opci&oacute;n en de la barra superior.
						<?php for ($i=0; $i < 6 ; $i++) { ?>
							<img src="../imagenes/flechacurva.png" alt="flechaarriba">
						<?php } ?>
					</div>
			</div>
			<div class="barra_busq">
				<div class="bus_cab">
					<input type="text" id="texto_buscar" name="texto_buscar" placeholder="Busca Clientes" size="40">
					<div class="box_btn_bus">
						<a class="btn_buscar" href=""></a>
					</div>
				</div>
			</div>
			<div class="tabla" id="tabla">

			</div>

		</div>
	</div>

	<div id="img_carga">
			<img src="../imagenes/carga.gif">
			<br>
			<span>Procesando...</span>
	</div>
</body>
</html>