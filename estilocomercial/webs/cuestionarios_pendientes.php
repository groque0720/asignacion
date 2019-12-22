<!DOCTYPE html>
<html lang="es">
<head>
	<title>Lista de Encuestas</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/dyv.ico" />
	<link rel="stylesheet" href="../css/styles.css">
	<link rel="stylesheet" href="../css/estilo_default.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
		<script src="../js/jquery-1.9.1.js"></script>
	<script src="../js/jquery-ui.js"></script>
	<script src="../css/jquery-ui.css"></script>

	<script>
		$(document).ready(function(){

			$("#barra_carga_excel").hide();

			$("#terminado_masivo").click(function(){
				if ($("#sl_encuesta").val()==0) {
					alert("Seleccione la encuesta a realizar el Terminado Masivo");
				}else{
					if (confirm("Seguro desea proceder al teminado masimo de Cuestionarios Pendientes?")) {
						operacion="terminado_masivo";
						id_encuesta = $("#sl_encuesta").val();
						$.ajax({
							url:"cuestionarios_abm.php",
							cache:false,
							type:"POST",
							data:{id_encuesta:id_encuesta, operacion:operacion},
							success:function(result){
								$("#lista_cuestionarios").html(result);
							}
			    		});
					};
				};
			})

			$(".editar, .nueva_app").click(function(e){
				e.preventDefault();


				if ($(this).attr("data-mov")=='1') {
					operacion="nuevo_custionario";
					$.ajax({
						url:"cuestionarios_abm.php",
						cache:false,
						type:"POST",
						data:{operacion:operacion},
						success:function(result){
							self.location="cuestionario.php?id="+result+"&cue=0 ";
						}
		    		});
				};

				if ($(this).attr("data-mov")=='2') {
					// self.location="cuestionario.php?id="+$(this).attr("data-id");
					window.open(
					  'cuestionario.php?id='+$(this).attr("data-id"),
					  '_blank' // <- This is what makes it open in a new window.
					);

				};
			});

			//-------------------------------------------------------------
			$("#sl_encuesta").change(function(){

				cad="";
				if ($(this).val()>0) {
					cad=$(this).val();
				};

				$.ajax({
					url:"cuestionarios_busqueda.php",
					cache:false,
					type:"POST",
					data:{cad:cad},
					success:function(result){
						$("#lista_cuestionarios").html(result);
					}
	    		});


			})
			//--------------------------------------------------------------

			$(".importar").click(function(event){
				event.preventDefault();
				$("#form_excel").dialog("open");
			})
			//-----------------------------------------------------------------------
			$("#enviar").click(function(){
				$("#form_excel").dialog("option", "height", 270);
				$("#barra_carga_excel").show();
			})

			//------------------------------------------------------------------------
			$( "#form_excel" ).dialog({
		     	autoOpen: false, // no abrir automáticamente
		     	resizable: true, //permite cambiar el tamaño
		     	width: 400,
		     	height:250, // altura
			    modal: true, //capa principal, fondo opaco
			    resizable: false,
                autoResize: true,
			    buttons: { //crear botón de cerrar

			    "Cancelar": function() {
				    $( this ).dialog( "close" );

			   		},
		          }
			});
			//------------------------------------------------------------------
			$("#buscar_c").keypress(function(e){

		       var keycode = (event.keyCode ? event.keyCode : event.which);
		      	if(keycode == '13'){
		      		cad=$("#sl_encuesta").val();
					det=$("#buscar_c").val();
					$.ajax({url:"cuestionarios_filtro.php",cache:false,type:"POST",data:{buscar:det, cad:cad },success:function(result){
			      	$("#lista_cuestionarios").html(result);
			    	}});
		      	}
	 		});
	 		//-------------------------------------------------------------------
	 			$('#buscar_c').autocomplete({
				source: "cuestionarios_p_autocompletar.php"
				});

		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php");
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");

	 ?>

	<div class="ed-container web-80 ">
		<div class="ed-item   centrar-texto">
			<h1>Encuestas Pendientes</h1>
			<hr>
		</div>
	</div>

	<div class="ed-container web-80 zona-nav">
		<div class="ed-item web-70">
			<a class="icon-izquierda espacio" href="../../dashboard">Panel de Apliciones</a>

			<?php

			$SQL="SELECT * FROM encuestas";
			$res_e=mysqli_query($con, $SQL);

			 ?>
			<label class="icon-buscar espacio opcion" for="encuesta"> Ver Encuesta</label>
			<select name="sl_encuesta" id="sl_encuesta">
				<option class="top" value="0">Todas</option>
				<?php
					while ($ecu=mysqli_fetch_array($res_e)) { ?>
					<option value="<?php echo $ecu["id_encuesta"]; ?>"><?php echo $ecu["encuesta"];?></option>

					<?php } ?>
			</select>
			<input type="button" id="terminado_masivo" class="terminado_masivo" value="Terminado Masivo">



		</div>
		<div class="ed-item web-30 derecha-contenido">
			<a class="icon-arriba espacio importar opcion" data-mov="1" href="">Importar</a>
			<a class="icon-enlace espacio nueva_app opcion" data-mov="1" href="">Nuevo Cuestionario</a>
		</div>



	</div>
	<div class="ed-container">
		<div class="ed-item">
			<div class="ed-container web-80 derecha-contenido">
				<div class="ed-item web-1-5 derecha-texto no-padding" >
					<span class="icon-buscar espacio"><input type="text" id="buscar_c" class="buscar_c" size="50" value="" placeholder="Buscar Cliente"></span>

				 </div>

			</div>


			<input type="hidden" id="id_encuesta" value="<?php echo $encuesta; ?>">
			<?php
				$SQL="SELECT * FROM cuestionarios WHERE activo=1  AND id_estado_cuestionario <> 3 ORDER BY fecha_muestra_origen LIMIT 400";
				$res=mysqli_query($con, $SQL);
			 ?>

			<div class="zona-tabla-90 cuestionario" id="lista_cuestionarios">


				<?php include("cuestionario_lista_cuerpo.php"); ?>

			</div>

		</div>

	</div>

<div id="form_excel"class="form_excel">
    <div class="ed-container total tit_dialog">
		<div class="ed-item centrar-texto">
			<img class="web-2-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>
	<span>Seleccione un archivo Excel</span>

    <form name="importa" method="post" action="cuestionario_importar_excel.php" enctype="multipart/form-data" >
        <input type="file" name="excel" />
        <input id="enviar" type='submit' name='enviar'  value="Importar"  />
        <input type="hidden" value="upload" name="action" />
    </form>
    <div class="ed-container total" id="barra_carga_excel">
		<div class="ed-item centrar-texto">
    	<img class="web-2-3" src="../imagenes/carga_linea.gif" alt="Subiendo...">
		</div>
	</div>
</div>

<?php 	mysqli_close($con);	 ?>

	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>
</body>
</html>