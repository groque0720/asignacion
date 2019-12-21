<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<link rel="shortcut icon" type="image/x-icon" href="../dyv.ico" />
	<title>Recepción</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../css/recepcion.css">

    <script type="text/javascript">
		$(document).ready(function(){

			$(".img_carga").hide(); // tambien Recepcion_Cuerpo


			function actualizar_paginas(pagina){
				idusuario=$("#idusu").val();
				$.ajax({url:"recepcion_cuerpo.php",cache:false,type:"GET",data:{pagina:pagina, idusuario:idusuario},success:function(result){
			    $("#recepcion_cuerpo").html(result);
			    }});
			};

			$(".indice").click(function(event){ // tambien Recepcion_Cuerpo
				event.preventDefault();
				pagina=$(this).attr("data-id");
				actualizar_paginas(pagina);
			})

			$(".flecha").click(function(event){ // tambien Recepcion_Cuerpo
				event.preventDefault();
				pagina=$(this).attr("data-id");
				actualizar_paginas(pagina);
			})

			$(".boton_b").click(function(){
				campo=$("#campo").val();
				buscar_c=$("#buscar_c").val();
				idusuario=$("#idusu").val();
				$.ajax({url:"recepcion_buscar.php",cache:false,type:"POST",data:{campo:campo, buscar_c:buscar_c, idusuario:idusuario},success:function(result){
			    $("#recepcion_cuerpo").html(result);
			    }});
			});

			$("#btncancelar").click(function(){
				id_linea = $("#idlinea").val();
				$("#ventana").dialog("close");
				$("#sector").val('');
				$("#cliente").val('');
				$("#med_cont").val('');
				//interes=$("#idusuario").val();
				$("#telefono").val('');
				$("#asignado").val('');
				$("#email").val('');
				$("#seg_siac").val('');
				$("#obs").val('');
				$("#img_"+id_linea).hide();
			});

			$("#nuevo_registro").click(function(){


				$("#idaccion").val(1);
				$("#sector").val('');
				$("#cliente").val('');
				$("#med_cont").val('');
				//interes=$("#idusuario").val();
				$("#telefono").val('');
				$("#asignado").val('');
				$("#email").val('');
				$("#seg_siac").val('');
				$("#obs").val('');
				$("#ventana").dialog("open");
				
			});

			$(".admin").click(function(event){ // tambien Recepcion_Cuerpo
				event.preventDefault();
				$("#idaccion").val(2);
				id_cont = $(this).attr('id');
				id_linea = $(this).attr('data-id');
				$("#img_"+id_linea).show();
				$("#idcontacto").val(id_cont);
				$("#idlinea").val(id_linea);
				
				// $($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[9].innerHTML = "<img src='../imagenes/carga.gif' alt='cargando' width='15px'>";

				// alert(id_cont);

				$("#sector").val('');
				$("#cliente").val('');
				$("#med_cont").val('');
				//interes=$("#idusuario").val();
				$("#telefono").val('');
				$("#asignado").val('');
				$("#email").val('');
				$("#seg_siac").val('');
				$("#obs").val('');

				
				$.ajax({url:"recepcion_carga_ver.php",cache:false,type:"POST",data:{id_cont:id_cont, id_linea:id_linea},success:function(result){
			    $("#parte_act").html(result);
			    }});

			    // $($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[9].innerHTML = "<a class='admin' id='"+id_cont+"' data-id='"+id_linea+"' href=''><img src='../imagenes/editar.png' title='Ver Registro' width='15px'></a>";

				$("#ventana").dialog("open");

			});		

			$("#btnenvio").click(function(event){
			var mens;
			mens = 0;

					
			if(($("#sector").val()=="" || $("#sector").val()==null) && mens ==0 ){
				alert("Ingrese un valor en el campo Sector");
				mens=1;
			}
			if(($("#cliente").val()=="" || $("#cliente").val()==null) && mens ==0 ){
				alert("Ingrese un valor en el campo Cliente");
				mens=1;
			}
			if(($("#med_cont").val()=="" || $("#med_cont").val()==null)  && mens ==0){
				alert("Ingrese un valor en el campo Contacto");
				mens=1;
			}
			if(($("#asignado").val()=="" || $("#asignado").val()==null) && mens ==0 ){
				alert("Ingrese un valor en el campo Asignado a");
				mens=1;
			}
			if(($("#telefono").val()=="" || $("#telefono").val()==null) && mens ==0){
				alert("Ingrese un valor en el campo Telefono");
				mens=1;
			}
			if(($("#email").val()=="" || $("#email").val()==null) && mens ==0){
				alert("Ingrese un valor en el campo Email");
				mens=1;
			}
			if(($("#seg_siac").val()=="" || $("#seg_siac").val()==null) && mens ==0 ){
				alert("Ingrese un valor en el campo Seguimiento SIAC");
				mens=1;
			}

			if (mens==0) { // que no falte nningun dato a pasar el formulario
				$("#ventana").dialog("close");
				idcontacto = $("#idcontacto").val();
				id_linea = $("#idlinea").val();
				idusuario=$("#idusu").val();
				fecha=$("#fecha").val();
				sector=$("#sector").val();
				cliente=$("#cliente").val();
				acercamiento=$("#med_cont").val();
				//interes=$("#idusuario").val();
				telefono=$("#telefono").val();
				asesor=$("#asignado").val();
				email=$("#email").val();
				seguimiento=$("#seg_siac").val();
				observacion=$("#obs").val();

				if ($("#idaccion").val()==1){ // pregunto si es 1 alta de nuevo registro
					$.ajax({url:"recepcion_alta.php",cache:false,type:"POST",
						data:{
							idusuario:idusuario,
							fecha:fecha,
							sector:sector,
							cliente:cliente,
							acercamiento:acercamiento,
							//interes:$("#idusuario").val();
							telefono:telefono,
							asesor:asesor,
							email:email,
							seguimiento:seguimiento,
							observacion:observacion,
						},
						success:function(result){
		      			// self.location = "recepcion.php";
		      			// $("#lineas_tabla").prepend(result);
		      			// $("#recepcion_cuerpo").load("recepcion_cuerpo.php")
		      			self.location = "recepcion.php"

		      			}
		      		})
				};
				
				if ($("#idaccion").val()==2){ // sino es 2 una modificacion de registro;

					$.ajax({url:"recepcion_edit.php",cache:false,type:"POST",
						data:{
							idcontacto:idcontacto,
							sector:sector,
							cliente:cliente,
							acercamiento:acercamiento,
							//interes:$("#idusuario").val();
							telefono:telefono,
							asesor:asesor,
							email:email,
							seguimiento:seguimiento,
							observacion:observacion,
						},
						success:function(result){
		      			// self.location = "recepcion.php";
		      			// $("#loading").html("hola");
		      			}
		      		});

					$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[1].innerHTML = $("#sector").val();
					$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[2].innerHTML = $("#cliente").val();
					$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[3].innerHTML = $("#med_cont").val();
					$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[4].innerHTML = $("#telefono").val();
					$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[5].innerHTML = $("#email").val();
					$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[6].innerHTML = $("#asignado").val();
					if ($("#seg_siac").val()==1) {
						$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[7].innerHTML = "<input type='checkbox' value='1' checked disabled>";
					}else{
						$($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[7].innerHTML = "<input type='checkbox' value='1' disabled>";
					};
					$("#img_"+id_linea).hide();

				};

			};//fin del si comprobacion de que no hay error
		});

			$( "#ventana" ).dialog({
		     	autoOpen: false, // no abrir automáticamente
		     	resizable: true, //permite cambiar el tamaño
		     	width: 445,
		     	height:490, // altura
			    modal: true, //capa principal, fondo opaco
			    resizable: false,
                autoResize: true,
			    // buttons: { //crear botón de cerrar
			    // "Enviar": function() { 
				   //  $( this ).dialog( "close" );
			   	// 	},
		     //      }
			});
		});
   </script>

</head>
<body>
<div id="agrupar">

	<?php 
	include("../includes/security.php");
	?>
	<header id="encabezado">
		<div id="imagen">
			<img src="../imagenes/logodyv.png" alt="Derka y Vargas S. A.">
		</div>

		<div id="titulo">
			REGISTRO DE CLIENTES  - RECEPCIÓN 
		</div>
		<div id="usuario">
			Terminal: <spam style="color:blue; font-style:italic;"><?php echo $_SESSION["usuario"]; ?></spam>
		</div>
	</header>
		<section id="seccion">

		

			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">
				
				<div id="nvo_reg" style="width: 40%; float: left;">
					<input type="button" class="boton" id="nuevo_registro" value="Nuevo Registro" style="margin: 10px; background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>
				<div id="alta_sol" style="width: 40%; float: right;">
					<label for="campo">Criterio</label>
					<select name="campo" id="campo">
						<option value="cliente">Cliente</option>
						<option value="sector">Sector</option>
						<option value="asesor">Asignado a</option>
						<option value="acercamiento">Medio</option>
					</select>
					<input type="text" id="buscar_c" name="buscar_c" placeholder="Buscar">
					<input type="button" class="boton_b" value="Buscar" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
					
				</div>

			</div>

			 <hr>
			 
			<div id="recepcion_cuerpo">
				<?php 
				$busqueda = 0;
				$idusuario=$_SESSION["id"];

				include("recepcion_cuerpo.php") ?>
			</div>
		</section>

	<div id="ventana" title="Registro de Cliente - Showroom">
		<div id="form">
			<input type="hidden" name="idlinea" id="idlinea" value="">
			<input type="hidden" name="idcontacto" id="idcontacto" value="">
			<input type="hidden" name="idaccion" id="idaccion" value="">
			<input type="hidden" name="idusu" id="idusu" value="<?php echo $_GET['idusu']; ?>">
			<div class="partes izquierda">
				<label id="fecha_reg" for="fecha">Fecha:</label>	
				<label for="sector">Sector:</label>
				<label for="sector">Cliente:</label>
				<label for="">Contacto:</label>
				<label for="asignado">Asignado a:</label>
				<label for="Telefono">Teléfono:</label>
				<label for="E-mail">E-mail:</label>
				<label for="">SIAC:</label>
				<label for="">Comentario:</label>	
			</div>
			<div class="partes derecha" id="parte_act">
				<input type="date" id="fecha" name="fecha" value="<?php echo date("Y-m-d"); ?>" >
				<select name="sector" id="sector" required>
					<option value=""></optsion>
					<option value="Ventas">Ventas</option>
					<option value="Servicios">Servicios</option>
					<option value="Respuestos">Respuestos</option>
					<option value="Plan de Ahorro">Plan de Ahorro</option>
					<option value="Otros">Otros</option>
				</select>
				<input id="cliente" name="cliente" type="text" placeholder="Cliente" required>
				<select name="med_cont" id="med_cont" required>
					<option value=""></option>
					<option value="Presencial">Presencial</option>
					<option value="Telefónico">Telefónico</option>
					<option value="E-mail">E-mail</option>
					<option value="Otros">Otros</option>
				</select>
				<input id="asignado" name="asignado" type="text" placeholder="Enviado a" required>
				<input id="telefono" name="telefono" type="text" placeholder="Teléfono de Contacto" required>

				<input id="email" name="email" type="email" placeholder="Correo Electrónio" required>
				<select name="seg_siac" id="seg_siac" required>
					<option value=""></option>
					<option value="0">No</option>
					<option value="1">SI</option>
				</select>
				<textarea name="obs" id="obs" cols="30" rows="10"></textarea>
				
				</div>
			    
				<div style="float:right; margin:10px 5px 0 0;">
					<input id="btncancelar" name="cancelar" type="submit" value="Cancelar">	
					<input id="btnenvio" name="submit" type="submit" value="Guardar Registro">	
				</div>
		</div>
	</div>		
</div>
<div id="loading">
	
</div>		
</body>
</html>