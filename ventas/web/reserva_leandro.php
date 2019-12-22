<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title>Reserva - DYV S. A.</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo_sol.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_sol_p.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--     // <script language="javascript" src="../js/jquery-1.10.2.js"></script> -->

    <script language="javascript">

	$(document).ready(function(){

		$("#obs_fact").hide();
		$("#menu_fact").hide();
		$("#nvares").hide();
		$("#img_carga").hide();
		$("#corregir").hide();
		$("#porqueno").hide();


		if (1==$("#ofreciotd").val() && $("#realizotd").val()==0) {
			$("#porqueno").show(0);
			$("#porque_no").attr("required", "true");
		}

		if ($("#enviado").val() >= 1) {
			$("#guardar_res").hide();
			$("#nvares").show();
			// $("#fecres").attr("disabled", true);
		};

			if ("3"==$("#enviado").val()) {
	  		$("#obs_fact").show();
	  		};

	  		if ("5"==$("#enviado").val()) {
	  			$('#reserva_vista').hide();

	  		};

	   	if ($("#idperfil").val()==14 || $("#idusuario").val()==11) {
			$("#menu_fact").show();
			};

		if ($("#idusuario").val()==45) {
			$("#corregir").show();
		};

		$("#boton_c").click(function(){
			var ley = $.trim($("#obs_r").text());
			$("#correccion").val(ley);
			$( "#mensaje" ).dialog("open");
		});


		$("#boton_c_ok").click(function(){

			if (confirm("Confirma el estado OK de la Reserva?")) {
				var nro = $("#nrores").val();
					$.ajax({url:"reserva_correccion_ok.php",
	       				cache:false,
	       				type:"POST",
	       				data:{nrores:nro},
	       				success:function(result){
      						$("#leyenda").html(result);
    						}
    					});


			return false;
			}

		});

//----------------------------------------------------------------------------------
		$( "#mensaje" ).dialog({
     	autoOpen: false, // no abrir automáticamente
     	resizable: true, //permite cambiar el tamaño
     	width: 400,
     	height:350, // altura
	    modal: true, //capa principal, fondo opaco
	    buttons: { //crear botón de cerrar
	    "Confirmar": function() {
	    	 if ($("#correccion").val()!="") {
	    	 		//$("#leyenda").html('<p>'+ $("#correccion").val() +'</p>');
	    	 		var mensaje=$("#correccion").val();
	    	 		var nro = $("#nrores").val();
	    	 		compra=$("#compra").val();
	    	 		usuario_a = $("#usuario_a").val();
	    	 		nombre = $("#nombre").val();
	    	 		grupo = $("#grupo").val();
	    	 		modelo = $("#modelo").val();
	    	 		detalleu = $("#detalleu").val();
	    	 		mesentrega = $("#mesentrega").val();
	    	 		anoentrega = $("#anoentrega").val();
	    	 		asesorres = $("#asesor_res").val();
	    	 		email = $("#email").val();
	    	 		$.ajax({url:"reserva_insertar_correccion.php",
	       				cache:false,
	       				type:"POST",
	       				data:{nrores:nro, mensaje:mensaje,
	       					compra:compra, usuario_a:usuario_a,
	       					nombre:nombre, grupo:grupo,
	       					modelo:modelo, detalleu:detalleu,
	       					mesentrega:mesentrega, anoentrega:anoentrega,
	       					asesorres:asesorres, email:email},
	       				success:function(result){
	       					$("#leyenda").html(result);
      						}
    					});
	    	 		$( this ).dialog( "close" );

	    	 }else{
	    	 	alert("Sin Datos no se puede enviar la correcci\u00f3n");
	    	 };
	    },
        "Cancelar": function() {
          $( this ).dialog( "close" );
        	}
          }
	    });
//-------------------------------------------------------------------------------------------------


		if ($("#idperfil").val()!=3) {
			$("#nvares").hide();
			$("#btn_gdr").hide();
 			$("#ingreso_codigo").hide();
 			$("table tr td:last-child").css({
   				"display": "none"
				});
 			$('select').attr('disabled', true);
 		};

//--------------------------PERFIL CONTROL RESERVAS---------------------------------------------

 		$('#factura_ok').click(function(event) {
			if ("5"!=$("#enviado").val()) {
		  		if (confirm("Confirma la aprobación de la Reserva??")) {
		  		var nrores = $("#nrores").val();
		  		alert("La reserva pasa al Estado de Aprobado");
				document.location.href = "reserva_ok.php?idres=" + nrores + "&";
				return false;
	  		}else{event.preventDefault();};
	  		}else{alert("Ya esta aprobada esta reserva!!!")};

	  	});


 		$('#reserva_vista').click(function(event) {
			if ("4"!=$("#enviado").val()) {
		  		if (confirm("Confirma el visto de esta reserva??")) {
		  		var nrores = $("#nrores").val();
		  		alert("La reserva pasa al Estado de Visto");
				document.location.href = "reserva_visto.php?idres=" + nrores + "&";
				return false;
	  		}else{event.preventDefault();};
	  		}else{alert("Ya es estado vista esta reserva!!!")};

	  	});



		$('#observar').click(function(event) {
	  		var obs = prompt("Ingrese Motivo de la observacion");
			if (obs!="" && obs != null) {
				var id=$("#idres").val();
				var nrores = $("#nrores").val();
				document.location.href = "reserva_observar.php?idres=" + nrores + "&obser=" + obs + "&";
				return false;
			};
	  	});
//--------------------------------------------------------------------------------------------------

		//Bloquear la tecla backspace
		$(document).unbind('keydown').bind('keydown',function(e){
			if((e.keyCode == 8) && ($("#observacion").is(":focus") || $("input").is(":focus") || $("#correccion").is(":focus") )){
				return true;
			}else{ if ((e.keyCode == 8)) {
				//alert("Para borrar una/s letras utilizar la tecla Delete..");
				return false;
			};};
		});


		//oculta la linea de ingresar codigo - si es usado o nuevo
		if ($("#modelo").val()!='' && $("#modelo").val()!= null && $("#idperfil").val()==3) {
   	 		$("#ingreso_codigo").show(0);
   		}else{

	   		 if ($("#detalleu").val()!='' && $("#detalleu").val()!= null && $("#idperfil").val()==3) {
	   	 		$("#ingreso_codigo").show(0);
	   		}else{
	   		  	$("#ingreso_codigo").hide(0);
	   		 };

			};

   		// compruebo si el foco tiene el textarea, si lo tiene se activa la tecla enter, sino carga una linea al detalle.-
		$("form").keypress(function(e) {



			if (e.which == 13) {

				if ($("#observacion").is(":focus")) {
				return true;
				}
				else {
        		det=$("#detalle_l").val();
			    mon=$("#monto").val();
			    nro=$("#nrores").val();
			    ad=$("#detalle_ad").val();
			    cred=$("#nrocred").val();
	       		$.ajax({url:"reserva_insertar_filas.php",
	       				cache:false,
	       				type:"POST",
	       				data:{nrocred: cred, det_l:det, monto:mon, nrores:nro, det_ad:ad },
	       				success:function(result){
      						$("#act_ajax").html(result);
    						}
    					});
				$("#monto").val('');
    			$("#detalle_l").val('');
    			$("#detalle_ad").val('');
    			$("#detalle_l").focus();
            	return false;
        	}
        };
    	});



		if ("Nuevo"==$("#compra").val()) {
			$("#nuevo").show(0);
			$("#nro_uni").show();
	  		$("#usado").hide(0);
	  		$("#titulo").html('SOLICITUD DE RESERVA DE VEHICULO OKM');
	  		$("#marca").attr("required", "true");
			$("#tipo").attr("required", "true");
			$("#grupo").attr("required", "true");
			$("#modelo").attr("required", "true");
			$("#color").attr("required", "true");
			$("#altuno").attr("required", "true");
			$("#altdos").attr("required", "true");
			$("#nrounidad").attr("required", "true");
		}else {
			$("#nuevo").hide(0);
			$("#usado").show(0);
			$("#nro_uni").hide(0);
			$("#titulo").html('SOLICITUD DE RESERVA DE VEHICULO USADO');
			$("#marca").removeAttr("required");
			$("#tipo").removeAttr("required");
			$("#grupo").removeAttr("required");
			$("#modelo").removeAttr("required");
			$("#color").removeAttr("required");
			$("#altuno").removeAttr("required");
			$("#altdos").removeAttr("required");
			$("#nrounidad").removeAttr("required");
		};


		$('.editar_f').click(function(event) {
		var importe = prompt("Ingrese Monto a Modificar");
		if (importe !=null && importe!='')
			{
			var id=$(this).attr('data-id');
			nro=$("#nrores").val();
			$.ajax({url:"reserva_editar_filas.php",cache:false,type:"POST",data:{idfila:id, monto:importe, nrores:nro },success:function(result){
	      		$("#act_ajax").html(result);
	    		}});
			}
 		 event.preventDefault();
		});

		$('.eliminar_f').click(function(event) {
		id = $(this).attr('data-id');
		nro=$("#nrores").val();
		$.ajax({url:"reserva_eliminar_filas.php",cache:false,type:"POST",data:{idfila:id, nrores:nro },success:function(result){
      		$("#act_ajax").html(result);
    		}});
 		 event.preventDefault();
		});


		$('#detalle_l').autocomplete({
			source: "buscarcodigos.php"
			});



		$('#detalle_l').change(function() {
			$("#detalle_ad").focus();
			});




		$('#enviar_res').click(function(event) {
			if (confirm("Confirma el envio de la Reserva??")) {
				if ($("#enviado").val() > 1) {

					var obs = prompt("Ingrese comentario de reenvio de reserva (ejm. Cambio de precio.");

					if (obs!="" && obs != null) {
						$("#enviado").val(2)
						$("#obs_cambio").val(obs);

					}else{
						alert("Debe Ingresar un comentario para poder reenviar la Reserva");
						event.preventDefault();
					};

				}else{$("#enviado").val(1);
					};
			}else{event.preventDefault();};
 		 });

		$("#agregar_l").click(function(){


		    det=$("#detalle_l").val();
		    mon=$("#monto").val();
		    nro=$("#nrores").val();
		    ad=$("#detalle_ad").val();
		    cred=$("#nrocred").val();
	       	$.ajax({url:"reserva_insertar_filas.php",cache:false,type:"POST",data:{nrocred: cred, det_l:det, monto:mon, nrores:nro, det_ad:ad,},success:function(result){
      		$("#act_ajax").html(result);
    		}});
			$("#monto").val('');
    		$("#detalle_l").val('');
    		$("#detalle_ad").val('');
    		$("#detalle_l").focus();
    		 		});



  	if ("Reemplazo"==$("#tipocompra").val()) {
		$(".mr").show(250);
	  	}else {
		$(".mr").hide(250);
		};



  	$("#compra").change(function()
  		 {
	  		if ("Nuevo"==$(this).val()) {
	  		$("#nro_uni").show();
	  		$("#nuevo").show(500);
	  		$("#usado").hide(500);
	  		$("#titulo").html('SOLICITUD DE RESERVA DE VEHICULO OKM');
	  		$("#ingreso_codigo").hide(0);
	  		$("#internou").val('');$("#detalleu").val('');$("#colorusa").val('');$("#aniousa").val('');$("#dominiou").val('');
	  		$("#marca").attr("required", "true");
			$("#tipo").attr("required", "true");
			$("#grupo").attr("required", "true");
			$("#modelo").attr("required", "true");
			$("#color").attr("required", "true");
			$("#altuno").attr("required", "true");
			$("#altdos").attr("required", "true");
			$("#nrounidad").attr("required", "true");
			$("#internou").removeAttr("required");
			$("#detalleu").removeAttr("required");
			$("#colorusa").removeAttr("required");
			$("#aniousa").removeAttr("required");
			$("#dominiou").removeAttr("required");
	  		}else {

			$("#nuevo").hide(500);
			$("#usado").show(500);
			$("#nro_uni").hide(0);
			$("#titulo").html('SOLICITUD DE RESERVA DE VEHICULO USADO');
			$("#marca").val(''); $("#tipo").val(''); $("#grupo").html('');$("#modelo").html('');
			$("#color").val('');$("#altuno").val('');$("#altdos").val('');$("#interno").val('');$("#nroorden").val('');
			$("#nrounidad").val('');
			$("#ingreso_codigo").show(250);
			$("#nrounidad").removeAttr("required");
			$("#marca").removeAttr("required");
			$("#tipo").removeAttr("required");
			$("#grupo").removeAttr("required");
			$("#modelo").removeAttr("required");
			$("#color").removeAttr("required");
			$("#altuno").removeAttr("required");
			$("#altdos").removeAttr("required");
			$("#internou").attr("required", "true");
			$("#detalleu").attr("required", "true");
			$("#colorusa").attr("required", "true");
			$("#aniousa").attr("required", "true");
			$("#dominiou").attr("required", "true");


				};

				nro=$("#nrores").val();
				$.ajax({url:"reserva_resert_filas.php",cache:false,type:"POST",data:{nrores:nro,},success:function(result){
      			$("#act_ajax").html(result);
    			}});
		    });

	$("#tipocompra").change(function ()
		 {
  			if ("Reemplazo"==$(this).val()) {
	  		$(".mr").show(250);

			}else {
			$(".mr").hide(250);
				};
	    });


	$("#tipo").change(function () {
		$("#tipo option:selected").each(function () {
		   	//sentencias para resetear SELECT #modelo
		   	$("#modelo").html("");
		    elegido=$(this).val();
		    $.post("reserva_buscar_grupos.php", { elegido: elegido }, function(data){
		    $("#grupo").html(data);
		            });
		 });

		   if ($("#modelo").val()!='' && $("#modelo").val()!= null) {
		  		$("#ingreso_codigo").show(0);
		  		}else{$("#ingreso_codigo").hide(0);};

	});

	$("#grupo").change(function () {

       	$("#grupo option:selected").each(function () {
        elegido=$(this).val();
        $.post("reserva_buscar_modelos.php", { elegido: elegido }, function(data){
        $("#modelo").html(data);
	            });
        	});

       if ($("#modelo").val()!='' && $("#modelo").val()!= null) {
	  		$("#ingreso_codigo").show(0);
	  		}else{$("#ingreso_codigo").hide(0);};
   	});

   		  $("#modelo").change(function(){

   		  	if ($(this).val()!='' && $(this).val()!= null) {
   		  		$("#ingreso_codigo").show(300);
   		  	}else{
   		  		$("#ingreso_codigo").hide(300);
   		  	};

		    mon=$("#modelo option:selected").val();
		    nro=$("#nrores").val();
	       	$.ajax({url:"reserva_cambiomodelo.php",cache:false,type:"POST",data:{elegido:mon, nrores:nro},success:function(result){
      		$("#act_ajax").html(result);
    		}});
			});

   		  $(".boton").click(function(event) {
			document.location.href ="asesores.php";
			});


//logica de test drive

 		$("#ofreciotd").change(function ()
 		 {
  			if (1==$(this).val() && $("#realizotd").val()==0) {

	  			$("#porqueno").show(250);
	  			$("#porque_no").attr("required", "true");

			}else {
				$("#porque_no").removeAttr("required");
				$("#porqueno").hide(250);

				};
	     });

	    $("#realizotd").change(function ()
 		 {
  			if (0==$(this).val() && $("#ofreciotd").val()==1) {
	  			$("#porqueno").show(250);
	  			$("#porque_no").attr("required", "true");

			}else {
				$("#porque_no").removeAttr("required");
				$("#porqueno").hide(250);

				};
	     });


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

 		<?php if ($reserva['corregir']) { ?>
	 		<div id="obs_r" style="background: red; color: white; width: 250px; position: fixed;">
				<p id="leyenda">
				<?php echo $reserva['corregir']; ?>
				</p>
			</div>
  		<?php } ?>


 		<div id="mensaje" title="Observación a Corregir">
 			<textarea name="correccion" id="correccion" cols="37" rows="10"></textarea>
 		</div>




		<div id="agrupar">

		<?php include("../includes/header.php") ?>
		<div class="fila no_imp">
			<div id="atras" style="margin: 10px; width: 40%; float:left; text-align: left;">

				<a href="javascript:window.history.back();">&laquo; Volver atrás</a>
				<!-- <input type="button" class="boton" value="<- Volver" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/> -->
			</div>

			<div id="menu_fact" style="margin: 10px; width: 40%; text-align: right;">
			<a href="#" class="factura_ok" id="factura_ok" style="margin-right: 10px;"><img src="../imagenes/ok.png" width="20px"></a>
			<a href="#" class="reserva_vista" id="reserva_vista" style="margin-right: 10px;"><img src="../imagenes/visto.png" width="20px"></a>
			<a href="#" class="observar" id="observar"><img src="../imagenes/lupa.png" width="20px"></a>
			</div>
			<div id="corregir">
				<a href="#" id="boton_c"><button>Observar</button></a>
				<a href="#" id="boton_c_ok"><button>OK</button></a>
			</div>
		</div>
		<div id="obs_fact" style="text-align: center; ">
		<span style="font-size:1.3em; color:red;" >Observaci&oacute;n</span>
		<textarea id="obser_fact" name="obser_facr" rows="4" style="width: 97%; font-size:1.3em; color:red;" readonly="readonly"><?php echo $reserva['obsres']; ?></textarea>


	</div>

		<section id="seccion">
			<div id="solicitud">

				<form id="form_solicitud" name="form_solicitud" action="reserva_edit.php" method="POST" autocomplete="off">

					<input id="nrores" name="nrores" type="hidden" value="<?php echo $_GET["IDrecord"]; ?>" >
					<input id="idcliente" name="idcliente" type="hidden" value="<?php echo $reserva['idcliente']; ?>" >
					<input id="identregau" name="identregau" type="hidden" value="<?php echo $reserva['identregau']; ?>" >
					<input id="enviado" name="enviado" type="hidden" value="<?php echo $reserva['enviada']; ?>" >
					<input id="nrocred" name="nrocred" type="hidden" value="<?php echo $reserva['idcredito']; ?>">
					<input id="idperfil" name="idperfil" type="hidden" value="<?php echo $_SESSION["idperfil"]; ?>">
					<input id="idusuario" name="idusuario" type="hidden" value="<?php echo $_SESSION["id"]; ?>">

					<?php include("../includes/solicitud/cabecera.php"); ?>
					<input id="asesor_res" name="asesor_res" type="hidden" value="<?php echo $usuario['nombre']; ?>">
					<input id="email" name="email" type="hidden" value="<?php echo $usuario['email']; ?>">

					<?php include("../includes/solicitud/unidad.php"); ?>


					<fieldset>

						<div id="ingreso_codigo"  style="margin-bottom: 30px;" >

							<div id="cargar_det" style="width:75%; float:left; margin-left:10px;">
							   <input id="detalle_l" name="detalle_l" type="text" value="" placeholder="C&oacute;digo" style="width:52%; float:left;"/>
								<input id="detalle_ad" name="detalle_ad" type="text" value="" placeholder="Detalle" style="width:45%; float:left; margin-left: 5px;"/>
							</div>
							<div style="width:13%; float:left; text-align:left;">
							<input type="text" id="monto" name="monto" placeholder="Monto" size="10" style="text-align: right;">
							</div>
							<div id="agregar_l"><img style="width:15px; margin: 3px auto;"src="../imagenes/guardar.png" width="15px"></div>

						</div>

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

					<input type="hidden" id="obs_cambio" name="obs_cambio" value="">
					<input type="hidden" id="usuario_a" name="usuario_a" value="<?php echo $_SESSION["usuario"]; ?>">
					<input type="submit" id="guardar_res" name="guardar" value="Guardar Reserva" size="30">

					<input type="submit" id="enviar_res" name="enviar_res" style="float:right; color:red;" value="<?php if ($reserva['enviada']==1) { echo "Guardar y Re-enviar Reserva"; }else { echo "Guardar y Enviar Reserva"; } ?>" size="30">

					</div>

				</form>

			</div>

		</section>


	</div>





</body>
<?php  mysqli_close($con);  ?>
</html>
