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

	<script>
		$(document).ready(function(){

			$("#barra_carga_excel").hide();

			$(".editar, .nueva_app").click(function(e){
				if ($(this).attr("data-mov")=='1') {
					e.preventDefault();
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
					self.location="cuestionario.php?id="+$(this).attr("data-id");
				};
			});

			//-------------------------------------------------------------
			$("#sl_encuesta").change(function(){

				cad="";
				if ($(this).val()>0) {
					cad=$(this).val();
				};

				$.ajax({
					url:"cuestionarios_busqueda_t.php",
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
					$.ajax({url:"cuestionarios_filtro_t.php",cache:false,type:"POST",data:{buscar:det, cad:cad },success:function(result){
			      	$("#lista_cuestionarios").html(result);
			    	}});
		      	}
	 		});
	 		//-------------------------------------------------------------------
	 			$('#buscar_c').autocomplete({
				source: "cuestionarios_t_autocompletar.php"
				});

		});
	</script>

</head>
<body class="desarroll">
	<?php include("../_seguridad/_seguridad.php");
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	extract($_GET);

		// $cad=' AND id_encuesta = '.$encuesta;
		$cad="";
        $cad .=" AND YEAR(fecha_cuestionario) = ".$año;
        $periodo = "Año ".$año;

		if ($mes!=0) {
			$cad .=" AND MONTH(fecha_cuestionario) = ".$mes;
			$SQL="SELECT * FROM meses WHERE id_mes=".$mes;
			$meses=mysqli_fetch_array(mysql_query($SQL));
			$periodo=$meses["mmm"]." ".$año;
		}
		$evaluado = "Derka y Vargas S. A.";
		if ($suc!=0) {
			$cad .=" AND id_sucursal = ".$suc;
			$SQL = "SELECT * FROM sucursales WHERE id_sucursal=".$suc;
			$sucursal = mysqli_fetch_array(mysql_query($SQL));
			$evaluado = $sucursal["sucursal"];
		}

		if ($ase!=0) {
			$cad .=" AND id_asesor =".$ase;
			$SQL="SELECT * FROM usuarios WHERE id_usuario =".$ase;
			$asesor=mysqli_fetch_array(mysql_query($SQL));
			$evaluado = $asesor["nombre"];
		}

	$SQL="SELECT * FROM view_cuestionarios WHERE ".$extra." ORDER BY fecha_cuestionario DESC LIMIT 900";
	$res=mysqli_query($con, $SQL);
 ?>
	<div class="ed-container web-80 ">
		<div class="ed-item   centrar-texto">
			<h1>Informe según Reporte Generado</h1>
			<hr>
		</div>
	</div>

	<div class="ed-container web-80 zona-nav">
		<div class="ed-item web-15 centrar-texto">
			<span>Estado: <span class="negrita-italica"><?php echo $estado;?></span></span>
		</div>
		<div class="ed-item web-20 centrar-texto">
			<span>Encuesta: <span class="negrita-italica"><?php echo $encuesta;?></span></span>
		</div>
		<div class="ed-item web-20 centrar-texto">
			<span>Período: <span class="negrita-italica"><?php echo $periodo; ?></span>
		</div>
		<div class="ed-item web-30 centrar-texto">
			<span>Evaluado: <span class="negrita-italica"><?php echo $evaluado; ?></span>
		</div>
		<div class="ed-item web-15 centrar-texto">
			<span>Cantidad: <span class="negrita-italica"><?php echo $cant; ?></span>
		</div>
		<div class="ed-item">
			<hr>
		</div>
	</div>
	<div class="ed-container">
		<div class="ed-item">

			<div class="ed-container web-80 derecha-contenido">
				<div class="ed-item web-1-5 derecha-texto no-padding" >

				 </div>

			</div>

			<input type="hidden" id="id_encuesta" value="<?php echo $encuesta; ?>">


			<div class="zona-tabla-90 cuestionario" id="lista_cuestionarios">

				<?php include("cuestionario_lista_cuerpo.php"); ?>

			</div>

		</div>

	</div>

<div id="form_excel">
	<form name="importar" id="importar" method="post" action="cuestionario_carga_excel.php" enctype="multipart/form-data" >
	    <input id="file" type="file" name="file"/>
	    <input type='submit' name='enviar'  value="Importar"  />
	    <input type="hidden" value="upload" name="action" />
	</form>
</div>



<?php 	mysqli_close($con);	 ?>

	<div class="ed-container total pie">
		<div class="ed-item   centrar-texto">
			<img class="imagen_logodyv web-1-6" src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>
	</div>
</body>
</html>