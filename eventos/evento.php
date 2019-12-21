<?php
	include("funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	$id_evento = $_GET["id"];

	$SQL="SELECT * FROM eventos WHERE id_evento=".$id_evento;
	$eventos=mysqli_query($con, $SQL);
	$evento=mysqli_fetch_array($eventos);

 ?>


<!DOCTYPE html>
<html lang="es">
<head>
	<title>Ver Propiedad</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="icon" type="image/png" href="imgcomunes/logopest.png"/>
	<link rel="stylesheet" href="css/styles.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="css/estilo_default.css">
	<script>

		$(document).ready(function(){

			$(".carga_gif").hide();
			$("#carga_up").hide();
//----------------------------------------------------------
			$("#cargar_foto").click(function(event){
				event.preventDefault();
				$("#form_foto").dialog("open");
			});
//----------------------------------------------------------
			$(".imagen_pro").click(function(event){
				event.preventDefault();
				nro_imagen=$(this).attr("data-nro");
				id=$(this).attr("id");
				if (confirm('Estás seguro que deseas eliminar la imagen?')) {
					$(".carga_gif").show();
					operacion="Eliminar_imagen";
					url=$(this).attr("data-url");
					$.ajax({
						url:"evento_abm.php",
						cache:false,
						type:"POST",
						data:{operacion:operacion, id_img:id, url:url},
						success:function(result){
							$(".carga_gif").hide();
							$("#img_"+nro_imagen).hide(200);
						}
		    		});
				};
			})
//------------------------------------------------------------------
			$( "#form_foto" ).dialog({
		     	autoOpen: false, // no abrir automáticamente
		     	resizable: true, //permite cambiar el tamaño
		     	width: 400,
		     	height:210, // altura
			    modal: true, //capa principal, fondo opaco
			    resizable: false,
                autoResize: true,
			    buttons: { //crear botón de cerrar

			    "Cancelar": function() {
				    $( this ).dialog( "close" );
				    $("#images").val('');
			   		},
		          }
			});
//------------------------------------------------------------------
			$("#form1").submit(function(event){
				event.preventDefault();
				if (true) {};
					var formData = new FormData($("#form1")[0]);
		            var ruta = "upload_img.php";
		            $("#carga_up").show();

		            $.ajax({
		                url: ruta,
		                type: "POST",
		                data: formData,
		                contentType: false,
		                processData: false,
		                success: function(datos)
		                {
		                   $("#zona_fotos").html(datos);
		                   $("#images").val('');
		                   $("#carga_up").hide();
		                   $( "#form_foto" ).dialog('close');
		                }
		            });

			})
		});
	</script>
</head>
<body class="desarroll">

	<div class="ed-container  ">
		<div class="ed-item centrar-texto">
			<h1>Datos del evento</h1>
		</div>
	</div>
	<div class="ed-container web-75 zona-nav">
		<div class="ed-item web-50">
			<a class="icon-izquierda espacio" href="index.php">Página Anterior</a>
		</div>

	</div>

<!-- $mov=2 quiere decir que van a realizar una modificación en el nombre de la aplicación -->



<form id="from_app" class="form_app" action="evento_abm.php" method="POST">
	<input type="hidden" name="id_evento" id="id_evento" value="<?php echo $id_evento; ?>">
	<input type="hidden" name="operacion" id="operacion" value="<?php echo "editar"; ?>">
	<div class="ed-container">
			<div class="caja web-1-3">
				<div class="zona-form-dos zona-form">
					<div class="linea">
						<div class="lbl">
							<label for="">Asistencia de evento:</label>
						</div>
						<div class="cdr_input">
							<select name="negocio" id="negocio" required>
								<option value=""></option>
								<option value="Campo" <?php if ("Campo"==$evento["negocio"]) { echo "selected";} ?>>Campo</option>
								<option value="Eventos internos" <?php if ("Eventos internos"==$evento["negocio"]) { echo "selected";} ?> >Eventos internos</option>
								<option value="Lanzamiento de Producto" <?php if ("Lanzamiento de Producto"==$evento["negocio"]) { echo "selected";} ?>>Lanzamiento de Producto</option>
								<option value="Promociones Plan de Ahorro" <?php if ("Promociones Plan de Ahorro"==$evento["negocio"]) { echo "selected";} ?>>Promociones Plan de Ahorro</option>
								<option value="Promociones Convencional" <?php if ("Promociones Convencional"==$evento["negocio"]) { echo "selected";} ?>>Promociones Convencional</option>
								<option value="Promociones MF" <?php if ("Promociones MF"==$evento["negocio"]) { echo "selected";} ?>>Promociones MF</option>
							</select>
						</div>
					</div>

					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Nombre del Evento:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="titulo" id="titulo" value="<?php echo $evento["titulo"]; ?>" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Ubicación:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="ubicacion" id="ubicacion" value="<?php echo $evento["ubicacion"]; ?>" required>
						</div>
					</div>

				</div>
			</div>
			<div class="caja web-1-3">
				<div class="zona-form-dos zona-form">

					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Fecha Inicio:</label>
						</div>
						<div class="cdr_input">
							<input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo $evento["fecha_inicio"]; ?>" required>
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Fecha Fin:</label>
						</div>
						<div class="cdr_input">
							<input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo $evento["fecha_fin"]; ?>" required>
						</div>
					</div>
				</div>
			</div>
			<div class="caja web-1-3">
				<div class="zona-form-dos zona-form">

					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Asistentes al evento:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="asistentes" id="asistentes" value="<?php echo $evento["asistentes"]; ?>">
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Contactos realizados:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="contactos" id="contactos" value="<?php echo $evento["contactos"]; ?>">
						</div>
					</div>
					<div class="linea">
						<div class="lbl">
							<label for="aplicacion">Ventas realizadas:</label>
						</div>
						<div class="cdr_input">
							<input type="text" name="ventas" id="ventas" value="<?php echo $evento["ventas"]; ?>">
						</div>
					</div>
				</div>
			</div>

			<div class="caja">
				<div class="zona-form-dos zona-form">
					<div class="linea">
						<div class="ed-item">
							<div class="lbl">
								<label for="aplicacion">Detalle:</label>
							</div>
							<div class="cdr_input">
								<textarea name="detalle" id="detalle" cols="" rows="7"><?php echo $evento["detalle"]; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>





	</div>



	<div class="zona-form-dos zona-form">

	<div class="linea">

		<div class="lbl ed-container">
			<div class="ed-item web-50">
				<label for="aplicacion">Imágenes:</label>
			</div>
			<div class="ed-item web-50 derecha-texto">
				<img id="carga_gif" class="carga_gif" src="imagenes/carga.gif" alt="barra_de_carga">
				<a id="cargar_foto" class="cargar_foto" href="">Cargar Fotos</a>
			</div>
		</div>

		<hr>
			<div class="ed-container" id="zona_fotos">

				<?php
				$SQL="SELECT * FROM imagenes WHERE activo=1 AND id_evento=".$evento["id_evento"];
				$imagenes=mysqli_query($con, $SQL);
				$nro_imagen=0;
				while ($img=mysqli_fetch_array($imagenes)) {
					$nro_imagen=$nro_imagen+1;?>
					<div class="ed-item web-1-6 div_img" id="<?php echo "img_".$nro_imagen; ?>">
						<a href="" class="imagen_pro" data-url="<?php echo $img["url"]; ?>" data-nro="<?php echo $nro_imagen;?>" id="<?php echo $img["id_img"] ?>">X</a>
						<img src="<?php echo $img["url"]; ?>" alt="foto_evento">


					</div>
				<?php } ?>

				<div class="ed-item error_carga">
				</div>

			</div>
		</div>
	</div>
	<div class="zona-form-dos zona-form">
		<div class="linea">
			<div class="ed-item derecha-contenido">
				<a class="nueva_app" data-mov="2" href="">
					<input type="submit" id="btn-enviar" class="btn-enviar" value="Guardar">
				</a>
			</div>
		</div>

	</div>


</form>
<div class="form_foto" id="form_foto">
	<div class="encabezado_dialog centrar-texto">
		Cargar fotos
	</div>
	<form id="form1" name="form1"  enctype="multipart/form-data">
	  <p>Seleccionar archivos de fotos:</p>
	  	<input type="hidden" name="id_evento" id="id_evento" value="<?php echo $evento["id_evento"]; ?>">
	  	<input type="file" id="images" name="images[]" multiple />
        <input type="submit" name="aceptar_foto" id="aceptar_foto" value="Subir..." />
        <div class="ca centrar-contenido">
        	<img id="carga_up" class="carga_up" src="imagenes/carga.gif" alt="barra_de_carga">
        </div>


	</form>
</div>

</div>
</body>
</html>