
<?php include("cuestionario_preguntas_carga.php"); ?>



<!DOCTYPE html>
<html lang="es">
<head>
	<title>Cuestionario</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../css/estilo_cuestionario.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link rel="stylesheet" href="../css/estilo_default.css">
	<link rel="stylesheet" type="text/css" media="print" href="../css/encuesta_imprimir.css">
		<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>

	<script>
		$(document).ready(function(){

			labeles();

			$("#img_carga").hide();

			$(".celda_1").addClass("pregunta_activa");
//-------------------------------------------------------------
			if ($("#id_estado_cuestionario").val()==2) {
				$("#motivo").hide();
			};
//------------------------------------------------------------
			$("#salir").click(function(){
				window.close();
			})
//------------------------------------------------------------
			function click_pregunta(activa){

				cant=$("#cant_preg").val();

				for (var i = 1; i <= cant; i++) {
					$(".celda_"+i).removeClass("pregunta_activa");
				};

				$(".celda_"+activa).addClass("pregunta_activa");

			}
//--------------------------------------------------------------

			function click_proxima(prox){

				cant=$("#cant_preg").val();

				for (var i = 1; i <= cant; i++) {
					$(".celda_"+i).removeClass("pregunta_proxima");
				};

				$(".celda_"+prox).addClass("pregunta_proxima");


			}
//----------------------------------------------------------------

	function labeles(){

			$("#lbl_email").show();
			$("#lbl_localidad").show();
			$("#lbl_año").show();
			$("#lbl_cencesionario").show();
			$("#lbl_dominio").show();
			$("#lbl_profesion").show();


		if ($("#id_encuesta").val()==1) {
			$("#lbl_localidad").hide();
			$("#lbl_año").hide();
			$("#lbl_cencesionario").hide();
			$("#lbl_dominio").hide();
		};

		if ($("#id_encuesta").val()==2) {
			$("#lbl_email").hide();
			$("#lbl_localidad").hide();
			$("#lbl_año").hide();
			$("#lbl_cencesionario").hide();
			$("#lbl_dominio").hide();
		};

		if ($("#id_encuesta").val()==3) {
			$("#lbl_profesion").hide();
		};

	}

//-----------------------------------------------------------------

		$("#id_estado_cuestionario").change(function(){

			if ($("#id_estado_cuestionario").val()==2) {
				$("#motivo").fadeOut( "slow" );
			}else{
				$("#motivo").fadeIn( "slow" );
			};

		})

//-----------------------------------------------------------------
		$("#id_encuesta").change(function(){
			if ($(this).val()!=0) {
				if (confirm("Seguro desea cambiar la encuesta, se eliminaran todas las preguntas anteriores?")) {
					$("#zona_pregunta").html("");
					$("#img_carga").show();
					labeles();
					id_enc=$(this).val();
					id_cue=$("#id_cuestionario").val();
					op="reset";
					$.ajax({
						url:"cuestionario_preguntas_carga.php",
						cache:false,
						type:"GET",
						data:{cue:id_enc, id:id_cue, op:op},
						success:function(result){
							$("#zona_pregunta").html(result);
							$("#img_carga").hide();
						}
			    	});
				};
			}else{alert("Seleccione una encuesta valida.");};
		})
//------------------------------------------------------------
		$("#id_modelo").change(function(){
			$("#id_version").html('');
			id_modelo=$(this).val();
			operacion="carga_version";

			$.ajax({
				url:"cuestionarios_abm.php",
				cache:false,
				type:"POST",
				data:{id_modelo:id_modelo, operacion:operacion},
				success:function(result){
					$("#zona_carga_version").html(result);
				}
	    	});
		})

//-----------------------------------------------------------------------


//------------------------------------------------------------------------
		$(".pipa").change(function(){
			// alert($(this).attr("name")+"-"+$(this).attr("data-nro")+"-"+$(this).val());
			// alert($(this).attr("data-si"));

			if ($(this).prop('checked')==true) {
				valor=1;
			}else{ valor=0;}


			var string=$(this).attr("data-si")
			var item= string.split("-");

			var prox = parseInt($(this).attr("data-preg"))+1;


				if (item[0]==item[2]|| $(this).attr("name")==2) {
					$(".celda_"+item[1]).addClass("pregunta_proxima");
					click_proxima(item[1]);
				}else{
					click_proxima(prox);
				};

			click_pregunta($(this).attr("data-preg"));
			formato=$(this).attr("name");
			id_resp=$(this).val();
			nro_pregunta=$(this).attr("data-nro");
			id_cuestionario = $("#id_cuestionario").val();
			id_estado=$("#id_estado_cuestionario").val();
			$.post("cuestionario_preguntas_procesar.php",
			{formato:formato,id:id_resp, nro:nro_pregunta, id_cuestionario:id_cuestionario, id_estado:id_estado,valor:valor},
			function(result){$("#cambio_estado").html(result);});
		})

		$(".pipa_ta").focus(function(){

			click_pregunta($(this).attr("data-preg"));

			var prox = parseInt($(this).attr("data-preg"))+1;

			if ($(this).attr("data-form")==3) {
				click_proxima(prox);
			}
			// else{
			// var string=$(this).attr("data-si")
			// var item= string.split("-");
			// click_proxima(item[1]);
			// };
		})

		$(".pipa_ta").change(function(){
			// alert($(this).attr("name")+"-"+$(this).attr("data-nro")+"-"+$(this).val());
			formato=$(this).attr("name");
			valor=$(this).val();
			nro_pregunta=$(this).attr("data-nro");
			id_cuestionario = $("#id_cuestionario").val();
			id_estado=$("#id_estado_cuestionario").val();
			$.post("cuestionario_preguntas_procesar.php",
			{formato:formato, nro:nro_pregunta, texto:valor, id_cuestionario:id_cuestionario, id_estado:id_estado});
		})

		});
	</script>

</head>
<body class="desarroll">
	<?php
		$SQL="SELECT * FROM cuestionarios WHERE id_cuestionario=".$_GET["id"];
		$res_cuestionarios=mysqli_query($con, $SQL);
		$c=mysqli_fetch_array($res_cuestionarios);

	 ?>

	<div class="ed-container titulo">
		<div class="ed-item centrar-texto" id="titulo">
			<h1>Cuestionario</h1>
		</div>
	</div>
	<div class="ed-container web-70 zona-nav">
		<div class="ed-item web-50 ">
			<?php if ($c["id_estado_cuestionario"]==3) { ?>
				<a class="icon-izquierda espacio link-back" href="cuestionarios_terminados.php">Página Anterior</a>
			<?php }else{?>
			 <a class="icon-izquierda espacio link-back" href="cuestionarios_pendientes.php">Página Anterior</a>
			 <?php } ?>

		</div>
		<div class="ed-item web-50 derecha-contenido nom_encuesta">
			<!-- <a class="icon-enlace espacio nueva_app" data-mov="1" href="">Nuevo Cuestionario</a> -->
			<label for="">Encuesta:</label>
			<?php
				$SQL="SELECT * FROM encuestas WHERE activo=1 AND baja=0";
				$res=mysqli_query($con, $SQL); ?>

				<select name="id_encuesta" id="id_encuesta">
					<option value=""></option>

					<?php while ($encuesta=mysqli_fetch_array($res)) { ?>
					<option value="<?php echo $encuesta["id_encuesta"] ?>" <?php if ($encuesta["id_encuesta"]==$c["id_encuesta"]) { echo "selected";} ?>><?php echo $encuesta["encuesta"]; ?></option>
					<?php  } ?>

				</select>
			<!-- 	<div class="carga_circulo_20"> -->
					<img id="img_carga" class="carga_circulo_20" src="../imagenes/carga_circulo.gif" alt="Cargando..">
<!-- 				</div> -->


			<?php  ?>
		</div>
	</div>
	<form id="from_app" class="form_app" action="cuestionarios_abm.php" method="POST">
	<input type="hidden" name="id_cuestionario" id="id_cuestionario" value="<?php echo $_GET["id"]; ?> ">
	<input type="hidden" id="operacion" name="operacion" value="editar">
	<input type="hidden" name="id_cue" value="<?php echo $c["id_encuesta"];?>">
		<div class="zona-form zona-form-70">
			<div class="ed-container">
				<div class="ed-item web-1-3">
					<div class="lbl">
						<label for="aplicacion">Fecha Cuestionario</label>
					</div>
					<div class="cdr_input">
						<input type="date" name="fecha_cuestionario" id="fecha_cuestionario" value="<?php echo $c["fecha_cuestionario"]; ?>">
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_muestra">
					<div class="lbl">
						<label for="aplicacion">Fecha Muestra:</label>
					</div>
					<div class="cdr_input">
						<input type="date" name="fecha_muestra_origen" id="fecha_muestra_origen" value="<?php echo $c["fecha_muestra_origen"]; ?>" required>
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_asesor">
					<div class="lbl">
						<label for="aplicacion">Asesor:</label>
					</div>
					<?php
						$SQL="SELECT * FROM usuarios WHERE activo = 1 AND baja=0 AND id_perfil=2"; // id_perfil = 2 busca a todos los que son asesores de venta en la tabla usuarios.
						$form=mysqli_query($con, $SQL);

					 ?>

					<div class="cdr_input">
						<select name="id_usuario" id="id_usuario">
							<option value="0"></option>
							<?php  while ($usuario=mysqli_fetch_array($form)) { ?>
								<option value="<?php echo $usuario["id_usuario"]; ?>" <?php if ($usuario["id_usuario"]==$c["id_usuario"]) { echo "selected";}?>><?php echo $usuario["nombre"]; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="ed-item web-2-3" id="lbl_cliente">
					<div class="lbl">
						<label for="aplicacion">Cliente:</label>
					</div>
					<?php $SQL="SELECT * FROM  cuestionarios_clientes WHERE id_cliente_cuestionario =".$c["id_cliente_cuestionario"];
						$res_cliente=mysqli_fetch_array(mysql_query($SQL));
					 ?>
					 <input type="hidden" name="id_cliente" id="id_cliente" value="<?php echo $c["id_cliente_cuestionario"]; ?>">
					<div class="cdr_input">
						<input type="text" name="cliente" id="cliente" data-cli="" value="<?php echo $res_cliente["nombre"]; ?>" required>
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_telefono">
					<div class="lbl">
						<label for="aplicacion">Teléfono:</label>
					</div>
					<div class="cdr_input">
						<input type="text" name="telefono" id="telefono" data-cli="" value="<?php echo $res_cliente["telefono"]; ?>" >
					</div>
				</div>

				<div class="ed-item web-1-3" id="lbl_email">
					<div class="lbl">
						<label for="aplicacion">Email:</label>
					</div>
					<div class="cdr_input">
						<input type="text" name="email" id="email" data-cli="" value="<?php echo $res_cliente["email"]; ?>" >
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_profesion">
					<div class="lbl">
						<label for="aplicacion">Profesión:</label>
					</div>
					<?php
					$SQL="SELECT * FROM profesiones";
					$prof=mysqli_query($con, $SQL);
					 ?>
					<div class="cdr_input">
						<select name="profesion" id="profesion" >
							<option value="0"></option>
							<?php  while ($profesiones=mysqli_fetch_array($prof)) { ?>
								<option value="<?php echo $profesiones["id_profesion"]; ?>" <?php if ($profesiones["id_profesion"]==$res_cliente["id_profesion"]) { echo "selected";} ?> ><?php echo $profesiones["profesion"]; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="ed-item web-1-3" id="lbl_localidad">
					<div class="lbl">
						<label for="aplicacion">Localidad:</label>
					</div>
					<div class="cdr_input">
						<input type="text" name="localidad" id="localidad" data-cli="" value="<?php echo $res_cliente["localidad"]; ?>" >
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_modelo">
					<div class="lbl">
						<label for="aplicacion">Modelo/versión:</label>
					</div>
					 <div class="cdr_input">
						<input type="text" name="modelo_version" id="modelo_version" data-cli="" value="<?php echo $c["modelo_version"]; ?>" >
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_dominio">
					<div class="lbl">
						<label for="aplicacion">Dominio:</label>
					</div>
					<div class="cdr_input">
						<input type="text" name="dominio" id="dominio" data-cli="" value="<?php echo $c["dominio"]; ?>" >
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_año">
					<div class="lbl">
						<label for="aplicacion">Año de Unidad:</label>
					</div>
					<div class="cdr_input">
						<input type="text" name="año_unidad" id="año_unidad" value="<?php echo $c["año_unidad"]; ?>" >
					</div>
				</div>
				<div class="ed-item web-1-3" id="lbl_cencesionario">
					<div class="lbl">
						<label for="aplicacion">Concesionario Vendedor:</label>
					</div>
					<div class="cdr_input">
						<input type="text" name="concesionario_vendedor" id="concesionario_vendedor" value="<?php echo $c["concesionario_vendedor"]; ?>" >
					</div>
				</div>
				<div class="ed-item web-1-3">
					<div class="lbl">
						<label for="aplicacion">Estado Cuestionario:</label>
					</div>
					<?php
						$SQL="SELECT * FROM cuestionarios_estados WHERE activo = 1";
						$form=mysqli_query($con, $SQL);
					 ?>
					<div class="cdr_input">
						<div id="cambio_estado">
							<select name="id_estado_cuestionario" id="id_estado_cuestionario" required>
								<option value="0"></option>
								<?php  while ($formato=mysqli_fetch_array($form)) { ?>
									<option value="<?php echo $formato["id_estado_cuestionario"]; ?>" <?php if ($formato["id_estado_cuestionario"]==$c["id_estado_cuestionario"]) { echo "selected";}?>><?php echo $formato["estado_cuestionario"]; ?></option>
								<?php } ?>
							</select>
						</div>
					<?php
						$SQL="SELECT * FROM cuestionarios_no_hechos";
						$no_hecho=mysqli_query($con, $SQL);
					 ?>
						<select name="motivo" id="motivo" >
							<option value="0"></option>
							<?php  while ($nh=mysqli_fetch_array($no_hecho)) { ?>
								<option value="<?php echo $nh["id_motivo_nohecho"]; ?>" <?php if ($nh["id_motivo_nohecho"]==$c["motivo"]) { echo "selected";} ?> ><?php echo $nh["motivo"]; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="ed-item web-1-3">
					<div class="lbl">
						<label for="aplicacion">Carácter:</label>
					</div>
					<div class="cdr_input">
						<select name="caracter" id="caracter">
							<option value="0" <?php if ($c["caracter"]==0) { echo "selected";} ?>></option>
							<option value="1" <?php if ($c["caracter"]==1) { echo "selected";} ?>>Importante</option>
						</select>

					</div>
				</div>
				<div class="ed-item total">
					<div class="lbl">
						<label for="aplicacion">Comentarios del encuestador:</label>
					</div>
					<textarea class="textarea_tabla" name="comentario" id="comentario" cols="" rows="2"><?php echo $c["comentario"]; ?></textarea>
				</div>
				<div class="ed-item derecha-contenido total">
					<input type="button" class="btn_enviar_salir" id="salir" value="Salir">
					<input type="submit" class="btn_enviar_form" id="" name="" value="Guardar">

				</div>

			</div>


		</div>

	</form>
	<div id="zona_pregunta" class="zona_pregunta">

		<?php include("cuestionario_cuerpo_preguntas.php"); ?>

	</div>

	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>




</body>
</html>