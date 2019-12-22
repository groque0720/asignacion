<!DOCTYPE html>
<html lang="es">
<head>
	<title>Publicaciones</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../css/estilo_default.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="../css/jquery-ui.css"></script>

	<style>
		.form_publicaciones{
			border: 1px solid #C8C9CD;
			box-shadow: 2px 2px 3px #C8C9CD;
			padding: 10px;
			border-radius: 5px;
			background: white;
			font-size: 1em;
		}
		.periodo, .fecha{
			width: 100%;
			font-size: 1em;
		}
		.url{
			width: 100%;
		}
		.btn_cancelar{
			font-weight: bold;
			color: red;
			font-size: 1em;
		}
		select {
			width: 100%;
		}
		.procesando{
			font-size: 1.2em;
			color: orange;
		}
	</style>

	<script>
		$(document).ready(function(){

			$("#proceso_carga").hide();

			$("#btn_cancelar").click(function(event){
				event.preventDefault();
				$("#dialog").dialog('close');
			})

			$("#dialog").dialog({
				autoOpen: false, // no abrir automáticamente
		     	resizable: false, //permite cambiar el tamaño
		     	width: 350,
		     	height:500, // altura
			    modal: true, //capa principal, fondo opaco
			    resizable: false,
                autoResize: false,
			    buttons: { //crear botón de cerrar
		          }
			});


			$(".nueva_app").click(function(event){
				event.preventDefault();
				$("#dialog").dialog('open');

			});

			$("#idsucursal").change(function(){
				if ($(this).val()==0) {
					$("#zona_asesor").html('<input type="hidden" id="asesor" name="asesor" value="0">');
					$("#zona_asesor").hide();
				}else{
					operacion='buscar_asesor';
					id_suc=$(this).val();
					$.ajax({
					url:"publicaciones_procesar.php",
					cache:false,
					type:"POST",
					data:{operacion:operacion, id_suc:id_suc},
					success:function(result){
						$("#zona_asesor").html(result);
						$("#zona_asesor").show();
					}
	    		});

				};
			})

			$("#form_publicaciones").submit(function(event){
				// event.preventDefault();
				$("#formulario_carga").hide();
				$("#proceso_carga").show();
			// 	$.ajax({
			// 		url:"publicaciones_upload.php",
			// 		cache:false,
			// 		type:"post",
			// 		data:$(this).serialize(),
			// 		success:function(result){
			// 			$("#zona_ajax").html(result);
			// 			$("#dialog").dialog('close');
			// 		}
	  //   		});

			})

		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php") ?>

	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Lista Publicaciones Realizadas</h1>
			<hr>
		</div>
	</div>
	<div class="ed-container web-80 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="javascript:history.back()">Página Anterior</a>
		</div>
		<div class="ed-item web-50 derecha-contenido">
			<a class="icon-enlace espacio nueva_app" data-mov="1" href="">Nueva Publicación</a>
		</div>

	</div>
	<div class="ed-container">
		<div class="ed-item">
			<?php
				include("../funciones/func_mysql.php");
				conectar();
				//mysql_query("SET NAMES 'utf8'");

			?>

			<div class="zona-tabla-90" id="zona_ajax">

				<?php include("publicaciones_lista_cuerpo.php"); ?>

			</div>

		</div>

	</div>
<div id="dialog" class="dialog_evaluaciones">
	<form action="publicaciones_upload.php" method="post" id="form_publicaciones" name="form_publicaciones" class="form_publicaciones" enctype="multipart/form-data">

		<div class="ed-container" id="formulario_carga">
			<div class="ed-container">
				<div class="ed-item">
					<label for="Periodo">Fecha</label>
				</div>
				<div class="ed-item ">
					<input type="date" id="fecha" name="fecha" class="fecha" required>
				</div>
			</div>
			<div class="ed-container">
				<div class="ed-item ">
					<label for="Periodo">Sucursal</label>
				</div>
				<div class="ed-item">
					<select name="idsucursal" id="idsucursal">
						<option value="0">Todas</option>
						<?php
							$SQL="SELECT * FROM sucursales";
							$res_suc=mysqli_query($con, $SQL);
							while ($suc=mysqli_fetch_array($res_suc)) { ?>
								<option value="<?php echo $suc['idsucursal'] ?>"><?php echo $suc['sucursal']; ?></option>
						<?php } ?>
					</select>
				</div>
				<div id="zona_asesor">
					<input type="hidden" id="idasesor" name="idasesor" value="0">
				</div>

			</div>
			<div class="ed-item">
				<label for="tema">Tema</label>
			</div>
			<div class="ed-item">
				<select name="id_tema" id="id_tema" required>
					<option value="">Seleccione</option>
					<?php
						$SQL="SELECT * FROM publicaciones_temas ORDER BY tema";
						$res_suc=mysqli_query($con, $SQL);
						while ($suc=mysqli_fetch_array($res_suc)) { ?>
							<option value="<?php echo $suc['id_publicacion_tema'] ?>"><?php echo $suc['tema']; ?></option>
					<?php } ?>
				</select>
			</div>

			<div class="ed-item">
				<label for="obs">Texto Adicional</label>
			</div>
			<div class="ed-item">
				<textarea name="obs" id="obs" cols="50" rows="10"></textarea>
			</div>
			<div class="ed-item">
				<hr>
			</div>

			<div class="ed-item">
				<label for="">Seleccione Archivo a publicar</label>
			</div>
			<div class="ed-item">
				<input type="file" name="publicacion" id="publicacion" />
			</div>

			<div class="ed-item">
				<label for="">URL</label>
			</div>
			<div class="ed-item">
				<input class="url" type="text" name="url_dos" id="url_dos" placeholder="o ingrese url"/>
			</div>
			<div class="ed-container">
				<div class="ed-item">
					<hr>
				</div>
				<div class="ed-item web-50 izquierda-contenido">
					<a class="btn_cancelar" id="btn_cancelar" href="">Cancelar</a>
				</div>
				<div class="ed-item web-50 derecha-contenido">
					<input type="submit" id="crear" name="crear" class="crear" value="Publicar">
				</div>
			</div>

		</div>

		<div class="ed-container" id="proceso_carga">
			<div class="ed-item centrar-texto">
				<span class="procesando">Procesando</span>
			</div>
			<div class="ed-item centrar-texto ">
				<img src="../imagenes/carga_circulo.gif" alt="Procesando">
			</div>
			<div class="ed-item centrar-texto">
				<span>Aguarde un momento por favor..</span>
			</div>
		</div>


	</form>

</div>

	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>

<?php mysqli_close($con); ?>
</body>
</html>