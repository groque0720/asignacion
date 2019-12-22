<!DOCTYPE html>
<html lang="es">
<head>
    <title>Facturaci&oacute;n</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo_facturacion.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_facturacion_p.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

 	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <script type="text/javascript">

    $(document).ready(function(){

    	$("#btn_gdr").hide();
    	$("#ingreso_codigo").hide();
    	$("#obs_fact").hide();
    	$("#menu_fact").hide();


 		if ($("#idperfil").val()==3 || $("#idusuario").val()==11 || $("#idusuario").val()==56) {

 			$("#btn_gdr").show();
 			$("#ingreso_codigo").show();
 			$("#menu_fact").hide();

 		};

 		if ($("#idperfil").val()!=3 ) {
 			$("table tr td:last-child").css({
   				"display": "none"
				});
 		};

 		if ($("#idperfil").val()==14) {
 			$("#menu_fact").show();
 			$("table tr td:last-child").css({
   				"display": "none"
				});
 		};

 		if ($("#idusuario").val()==11 || $("#idusuario").val()==56) {
 			$("table tr td:last-child").css({
   				"display": ""
				});
 		};


 		if ("1"==$("#estado").val() || "2"==$("#estado").val()) {
	  	$("#factura_ok").hide();
	  	};

 		if ("2"==$("#estado").val()) {
	  		$("#obs_fact").show();
	  		};

	  	if ("3"==$("#estado").val()) {
	  	$("#facturar").hide();
	  	$("#observar").hide();
	  	$("#factura_ok").show();
	  	};




	  	$('#facturar').click(function(event) {
	  	if (confirm("Confirma la facturacion de este Detalle??")) {
	  			$("#fecfact").removeAttr("disabled");
	  			$("#factura_ok").show();
	  			$("#facturar").hide();

			}else{event.preventDefault();};
	  	});


	  	$('#factura_ok').click(function(event) {
	  		if ($("#fecfact").val() != "") {
	  			var id=$("#idfact").val();
				var nrores = $("#nrores").val();
				var fec = $("#fecfact").val();
				document.location.href = "facturacion_facturar.php?idfact=" + id + "&fecfact=" + fec + "&nrores=" + nrores + "&";
				return false;
			}else{
				alert("Ingrese fecha de Facturacion..");
				event.preventDefault();};
	  	});

	  	$('#observar').click(function(event) {
	  		var obs = prompt("Ingrese Motivo de la observacion");
				if (obs!="" && obs != null) {
					var id=$("#idfact").val();
					var nrores = $("#nrores").val();
					document.location.href = "facturacion_observar.php?idfact=" + id + "&obser=" + obs + "&nrores=" + nrores + "&";
					return false;
				};
	  	});

    	$("form").keypress(function(e) {
       		if (e.which == 13) {
        		det=$("#detalle_l").val();
				mon=$("#monto").val();
				nro=$("#idfact").val();
				res=$("#nrores").val();
				ad=$("#detalle_ad").val();
				$.ajax({url:"facturacion_insertar_filas.php",
					cache:false,
					type:"POST",
					data:{op:1, det_l:det, monto:mon, idfact:nro, det_ad:ad, nrores:res },
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
    	});


    	if ("propio"==$("#tipo_fact").val()) {
	  		$("#cliente_t").show(0);
	  		$("#cliente_a").hide(0);
	  		$("#ex_titular").hide(0);
	  	}else {
			$("#cliente_t").hide(0);
			$("#cliente_a").show(0);
			$("#ex_titular").show(0);
		};


	    /*---------Compruebo si es nuevo o usado y oculto segun opcion------*/
	    if ("Nuevo"==$("#compra").val()) {
			$("#nuevo").show(0);
		  	$("#usado").hide(0);
	  		$("#interno").prop("required", true);
	  		$("nroorden").prop("required", true);
	  		$("#internou").removeAttr("required");
	  		$("#detalleu").removeAttr("required");

		}else {
				$("#nuevo").hide(0);
				$("#usado").show(0);
	  		$("#interno").removeAttr("required");
	  		$("nroorden").removeAttr("required");
	  		$("#internou").prop("required", true);
	  		$("#detalleu").prop("required", true);

		}; /* --------------*/

		/*---------Oculto la parte de si es uno cero o usado-----*/
		$("#opcion").hide();
		/*------------------*/

		$('#detalle_l').autocomplete({
				source: "buscarcodigos.php"
			});


	    $("#tipo_fact").change(function()
	  		{
		  		if ("propio"==$(this).val()) {
		  		$("#cliente_t").show(500);
		  		$("#cliente_a").hide(500);
		  		$("#ex_titular").hide(500);
		  		}else {
				$("#cliente_t").hide(500);
				$("#cliente_a").show(500);
				$("#ex_titular").show(500);
				};
			});



	    $("#agregar_l").click(function()
	    	{

				det=$("#detalle_l").val();
				mon=$("#monto").val();
				nro=$("#idfact").val();
				res=$("#nrores").val();
				ad=$("#detalle_ad").val();
				$.ajax({url:"facturacion_insertar_filas.php",
					cache:false,
					type:"POST",
					data:{op:1, det_l:det, monto:mon, idfact:nro, det_ad:ad, nrores:res },
					success:function(result){
		     			$("#act_ajax").html(result);
		   				}
		   			});
				$("#monto").val('');
	   			$("#detalle_l").val('');
	   			$("#detalle_ad").val('');
	   			$("#detalle_l").focus();
	    	});



	    $('.editar_f').click(function(event) {
		var importe = prompt("Ingrese Monto a Modificar");
		if (importe !=null && importe!='')
			{
			var id=$(this).attr('data-id');
			nro=$("#nrores").val();
			nrofact=$("#idfact").val();
			$.ajax({url:"facturacion_editar_filas.php",cache:false,type:"POST",data:{idfila:id, monto:importe, idfact:nrofact, nrores:nro },success:function(result){
	      		$("#act_ajax").html(result);
	    		}});
			}
 		 event.preventDefault();
		});


		$('.eliminar_f').click(function(event) {
		id = $(this).attr('data-id');
		nro=$("#nrores").val();
		nrofact=$("#idfact").val();
		$.ajax({url:"facturacion_eliminar_filas.php",cache:false,type:"POST",data:{idfila:id, idfact:nrofact, nrores:nro },success:function(result){
      		$("#act_ajax").html(result);
    		}});
 		 event.preventDefault();
		});

		$('#enviar_res').click(function(event) {

			if ($("#fecped").val() == "") {
			 alert("Cargue la fecha de pedido");
			 $("#fecped").focus;
			 event.preventDefault();
			}else{

			if (confirm("Confirma el pedido de facturaci\u00f3n??")) {
				$("#estado").val(1);
			}else{event.preventDefault();};};

		});

		$(".boton").click(function(event) {
			document.location.href ="asesores.php";
			});


		$("#form_facturacion").submit(function(event){

			if ("Nuevo"==$("#compra").val()) {
				if ($("#internodos").val()=='' || $("#nroordendos").val()=='') {
					event.preventDefault();
					alert("En la RESERVA ingrese INTERNO y NRO DE ORDEN, para poder realizar el pedido de facturación.");
				}
			}


		});


	});

    </script>



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
		////mysql_query("SET NAMES 'utf8'");
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

		$SQL="SELECT * FROM facturas WHERE idfactura=".$reserva['idfactura'];
		$facturas=mysqli_query($con, $SQL);
		$factura=mysqli_fetch_array($facturas);



 		?>
	<div id="cabecera">
	<?php include("../includes/facturacion/header.php"); ?>
			<div id="atras" style="margin: 10px;">
				<a href="javascript:window.history.back();">&laquo; Volver atrás</a>
				<!-- <input type="button" class="boton" value="<- Volver" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/> -->
			</div>

	</div>
<div id="detalle_fact">

	<form id="form_facturacion" name="form_facturacion" action="facturacion_edit.php" method="POST">
		<input id="nrores" name="nrores" type="hidden" value="<?php echo $_GET["IDrecord"]; ?>" >
		<input id="idcliente" name="idcliente" type="hidden" value="<?php echo $reserva['idcliente']; ?>" >
		<input id="identregau" name="identregau" type="hidden" value="<?php echo $reserva['identregau']; ?>" >
		<input id="idfact" name="idfact" type="hidden" value="<?php echo $factura['idfactura']; ?>" >
		<input id="estado" name="estado" type="hidden" value="<?php echo $factura['estado']; ?>" >
		<input id="idperfil" name="idperfil" type="hidden" value="<?php echo $_SESSION["idperfil"]; ?>">
		<input id="idusuario" name="idusuario" type="hidden" value="<?php echo $_SESSION["id"]; ?>">

	<div id="encabezado">
				<div id="logos" style="border-bottom: 1px solid #ccc; padding-left: 10px;">
					<img src="../imagenes/encabezado_fact.png" width="780px" height="60x">
				</div>

				<div id="encabezados" style="width=45%; float: left; font-size:.6em">
					<div>
					<img src="../imagenes/logodyv.png" alt="Derka y Vargas S. A." style="width:150px;">
					</div>
					M. Belgrano Nro 872-Tel.(0364)4420548/4420549/4420840/4420602-3700 Pcia. R. S. Pe&ntilde;a-Chaco<br>
					Ruta N. Avellaneda Km. 11.9-Tel.(0362)4764840 al 47 - 3500 Resistencia - Chaco<br>
					Guemes Nro 1440 - Tel. (03731)422107-420875 - 3700 Charata - Chaco<br>
					Av. 25 de Mayo Nro 1101 - Tel.(03735)423200 - 3540 Villa Angela - Chaco<br>
					<span style="font-weight: bold;">E-mail: administracion&#64;derkayvargas.com.ar / www.derkayvargas.com.ar</span>
				</div>

				<div id="encabezados" style="width=45%; float: right; text-align: right;">
					<br>
					<div>
						<label style="width: 30%; margin-right: 10px;">Fecha de Pedido:</label><input style="width: 50%;" type="date" id="fecped" name="fecped" value="<?php echo $factura['fecped']; ?>" required>
					</div>
					<div style="100%">
						<label style="width: 30%; margin-right: 10px;">Fecha de Facturaci&oacute;n:</label><input style="width: 50%;" type="date" id="fecfact" name="fecfact" value="<?php echo $factura["fecfact"]; ?>" disabled><br>
						<div id="menu_fact" style="margin-right: 10px;">
						<a href="#" class="factura_ok" id="factura_ok" style="margin-right: 10px;"><img src="../imagenes/ok.png" width="20px"></a>
						<a href="#" class="facturar" id="facturar" style="margin-right: 10px;"><img src="../imagenes/facturacion-caja.png" width="20px"></a>
						<a href="#" class="observar" id="observar"><img src="../imagenes/lupa.png" width="20px"></a>
						</div>
					</div>
					<div>
						<label style="width: 30%; margin-right: 10px;">Tipo de Factura:</label><input style="width: 50%;" type="text" value="<?php echo $reserva["factura"]; ?>" disabled><br>
					</div>
				</div>
	</div>

	<div id="titulo" style="text-align: center;font-size: 1.1em;font-weight: bold; text-decoration: underline;">
		<span >DETALLE DE OPERACION</span>
	</div>

	<div id="obs_fact" style="text-align: center; ">
		<span style="font-size:1.3em; color:red;" >Observaci&oacute;n</span>
		<textarea id="obser_fact" name="obser_facr" rows="4" style="width: 97%; font-size:1.3em; color:red;" readonly="readonly"><?php echo $factura['obser_fact'] ?></textarea>


	</div>

	<div id="cliente">

				<fieldset>

					<div id="nom_fact">

							<label>Factura a Nombre:</label>
							<select id="tipo_fact" name="tipo_fact" width="20px">
								<option value="propio" <?php  if ($factura['anombre'] == "propio") { echo "selected"; } ?>>Propio</option>
								<option value="aotro" <?php  if ($factura['anombre'] == "aotro") { echo "selected"; } ?>>De otro</option>
							</select>


						<div id="ex_titular">
							<input value="Reserva realizada por : <?php echo $clientes['nombre']; ?>" size="80" style="border:none;">
						</div>

					</div>
					<hr>

					<!-- Nombre propio de facturacion-->
					<div id="cliente_t">

						<div class="fila">
							<div style="width: 100%, text-align: left;">
							<label>Ap. y Nom.:</label>
							<input type="text" id="nombre_cli" name="nombre_cli" value="<?php echo $clientes['nombre']; ?>" size="80" disabled>
							</div>
						</div>
						<div class="fila">
							<div style="width: 50%;">
								<label>Direcci&oacute;n:</label>
								<input type="text" id="direccion" name="direccion" size="47" value="<?php echo $clientes["direccion"]; ?>" disabled>
							</div>
							<div style="width: 23%;">
								<label>Loc.:</label>
								<input type="text" id="localidad" name="localidad" size="16" value="<?php echo $clientes["localidad"]; ?>" disabled>
							</div>
							<div style="width: 22%; float:right !important; text-align: right;" >
								<label>Prov.:</label>
								<input type="text" id="provincia" name="provincia" size="15" value="<?php echo $clientes["provincia"]; ?>" disabled>
							</div>
						</div>
						<div class="fila">
							<div style="width: 27%; font-size: 1.1em !important; ">
								<label>Fec. Nac.:</label>
								<input  type="date" id="fecnac" name="fecnac" value="<?php echo $clientes["fecnac"]; ?>" disabled>
							</div>
							<div style="width: 22%;">
								<label>Tipo Doc.:</label>
								<select id="tipodoc" name="tipodoc" style="width:55px" disabled>
									<option value=""></option>
									<option value="DNI" <?php  if ($clientes['tipodoc'] == "DNI") { echo "selected"; } ?>>D.N.I</option>
									<option value="LC" <?php  if ($clientes['tipodoc'] == "LC") { echo "selected"; } ?>>L.C.</option>
									<option value="LE" <?php  if ($clientes['tipodoc'] == "LE") { echo "selected"; } ?>>L.E.</option>
									<option value="PAS" <?php  if ($clientes['tipodoc'] == "PAS") { echo "selected"; } ?>>PAS.</option>
								</select>
							</div>
							<div style="width: 22%;">
								<label>Nro. Doc.:</label>
								<input type="text" id="nrodoc" name="nrodoc" size="7" value="<?php echo $clientes["nrodoc"]; ?>" disabled>
							</div>
							<div style="width: 22%;">
								<label>Cuil / Cuit:</label>
								<input type="text" id="cuil" name="cuil" size="10" value="<?php echo $clientes["cuil"]; ?>" disabled>
							</div>

						</div>

						<div class="fila">

							<div style="width: 50%;">
								<label>E-mail:</label>
								<input type="email" id="mail" name="mail" size="48" value="<?php echo $clientes["mail"]; ?>" disabled>
							</div>
							<div style="width: 23%;">
								<label>Tel.:</label>
								<input type="text" id="tfijo" name="tfijo" size="18" value="<?php echo $clientes["tfijo"]; ?>" disabled>
							</div>
							<div style="width: 23%; float:right !important; text-align: right;">
								<label>Cel.:</label>
								<input type="text" id="tcelu" name="tcelu" size="18" value="<?php echo $clientes["tcelu"]; ?>" disabled>
							</div>

						</div>

					</div>

					<!-- Fin a Nombre propio de facturacion-->

					<div id="cliente_a">

						<div class="fila">
							<div style="width: 100%, text-align: left;">
							<label>Ap. y Nom.:</label>
							<input type="text" id="nombre_a" name="nombre_a" value="<?php echo $factura['nombre']; ?>" size="80" >
							</div>
						</div>
						<div class="fila">
							<div style="width: 50%;">
								<label>Direcci&oacute;n:</label>
								<input type="text" id="direccion" name="direccion" size="47" value="<?php echo $factura["direccion"]; ?>">
							</div>
							<div style="width: 23%;">
								<label>Loc.:</label>
								<input type="text" id="localidad" name="localidad" size="16" value="<?php echo $factura["localidad"]; ?>">
							</div>
							<div style="width: 22%; float:right !important; text-align: right;" >
								<label>Prov.:</label>
								<input type="text" id="provincia" name="provincia" size="15" value="<?php echo $factura["provincia"]; ?>">
							</div>
						</div>
						<div class="fila">
							<div style="width: 27%; font-size: 1.1em !important; ">
								<label>Fec. Nac.:</label>
								<input  type="date" id="fecnac" name="fecnac" value="<?php echo $factura["fecnac"]; ?>">
							</div>
							<div style="width: 22%;">
								<label>Tipo Doc.:</label>
								<select id="tipodoc" name="tipodoc" style="width:55px" >
									<option value=""></option>
									<option value="DNI" <?php  if ($factura['tipodoc'] == "DNI") { echo "selected"; } ?>>D.N.I</option>
									<option value="LC" <?php  if ($factura['tipodoc'] == "LC") { echo "selected"; } ?>>L.C.</option>
									<option value="LE" <?php  if ($factura['tipodoc'] == "LE") { echo "selected"; } ?>>L.E.</option>
									<option value="PAS" <?php  if ($factura['tipodoc'] == "PAS") { echo "selected"; } ?>>PAS.</option>
								</select>
							</div>
							<div style="width: 22%;">
								<label>Nro. Doc.:</label>
								<input type="text" id="nrodoc" name="nrodoc" size="7" value="<?php echo $factura["nrodoc"]; ?>">
							</div>
							<div style="width: 22%;">
								<label>Cuil / Cuit:</label>
								<input type="text" id="cuil" name="cuil" size="10" value="<?php echo $factura["cuil"]; ?>">
							</div>

						</div>

						<div class="fila">

							<div style="width: 50%;">
								<label>E-mail:</label>
								<input type="email" id="mail" name="mail" size="48" value="<?php echo $factura["mail"]; ?>">
							</div>
							<div style="width: 23%;">
								<label>Tel.:</label>
								<input type="text" id="tfijo" name="tfijo" size="18" value="<?php echo $factura["tfijo"]; ?>">
							</div>
							<div style="width: 23%; float:right !important; text-align: right;">
								<label>Cel.:</label>
								<input type="text" id="tcelu" name="tcelu" size="18" value="<?php echo $factura["tcelu"]; ?>">
							</div>

						</div>

					</div>
					<!-- A nombre de otro la facturacion -->

				</fieldset>
	</div>

<div id="cabeceras" >
	<fieldset>
		<div id="asesor" style="width:32%; float: left;">
			<label>Promotor:</label> <spam style="font-size: 1.3em; font-style:italic; font-weight: bold;" disabled>
			<?php echo $usuario['nombre']; ?>
						 </spam>
			<input id="asesor_vta" name="asesor_vta" type="hidden" value="<?php echo $usuario['nombre']; ?>">
		</div>

		<div style="width: 32%; float: left; text-align: center;" >
			<div id="asesor">
				<label>Venta: </label>
				<select id="venta" name="venta" 	disabled>
					<option value=""></option>
					<option value="Convensional" <?php  if ($reserva['venta'] == "Convensional") { echo "selected"; } ?>>Convensional</option>
					<option value="Reventa" <?php  if ($reserva['venta'] == "Reventa") { echo "selected"; } ?>>Reventa</option>
					<option value="Plan Empleado" <?php  if ($reserva['venta'] == "Plan Empleado") { echo "selected"; } ?>>Plan Empleado</option>
					<option value="Especial" <?php  if ($reserva['venta'] == "Especial") { echo "selected"; } ?>>Especial</option>
				</select>
			</div>
		</div>

		<div style="width: 32%; float:right; text-align: right;">
			<label>Fecha. Reserva:</label>
			<input type="date" id="fecres" name="fecres" value="<?php echo $reserva["fecres"]; ?>" disabled>
		</div>

		</fieldset>
	</div>


<div id="unidad">
				<fieldset>
						<div id="opcion" name="opcion" disabled>
						<label>Operaci&oacute;n:</label>
						<select id="compra" name="compra" required>
							<option value="Nuevo" <?php  if ($reserva["compra"] == "Nuevo") { echo "selected"; } ?>>Nuevo</option>
							<option value="Usado" <?php  if ($reserva["compra"] == "Usado") { echo "selected"; } ?>>Usado</option>
						</select>

						<hr>
					</div>

					<div id="unidad">
						<div id="nuevo">

							<div id="lineauno">

								<div id="marca" style="width:15%;">
									<label>Marca:</label>
									<input type="text" name="marca" id="marca"  value="<?php echo $reserva["marca"] ?>" size="6" disabled>
								</div>
								<div id="tipos" style="width:23%;">
									<label>Tipo:</label>
									<select id="tipo" name="tipo" style="width:80%" disabled>
										<option value=""></option>
										<?php
										while ($ase=mysqli_fetch_array($tipos)) { ?>
										<option value="<?php echo $ase['idtipo'];?>" <?php  if ($reserva["idtipo"] == $ase['idtipo']) { echo "selected"; } ?>><?php echo $ase['tipo'];?> </option>
										<?php }  ?>
									</select>
								</div>
								<div id="grupos" name="grupos" style="width:23%;">
									<label>Grupo:</label>
									<select id="grupo" name="grupo" style="width:70%" disabled>
										<?php // buscar el grupo que pertenece la reserva
										    $SQL="SELECT grupos.idgrupo as idgrupo, grupos.grupo as grupo, tipos.idtipo
        									FROM (grupos INNER JOIN modelos ON grupos.idgrupo = modelos.idgrupo) INNER JOIN tipos ON modelos.idtipo = tipos.idtipo
       										GROUP BY grupos.grupo, tipos.idtipo
       										HAVING (((tipos.idtipo)=".$reserva["idtipo"]."))";
       										$grupos=mysqli_query($con, $SQL);
       										 while ($grup=mysqli_fetch_array($grupos)) { ?>
       												<option value="<?php echo $grup["idgrupo"]; ?>"  <?php  if ($reserva["idgrupo"] == $grup["idgrupo"]) { echo "selected"; } ?> ><?php echo $grup["grupo"];?> </option>
       									<?php }  ?>
									</select>
								</div>
								<div id="modelos" name="modelos" style="width:35%;">
									<label>Modelo:</label>
									<select id="modelo" name="modelo" style="width:80%" disabled>
										<?php
											$SQL="SELECT * FROM modelos Where idgrupo=".$reserva["idgrupo"];
    										$modelos=mysqli_query($con, $SQL);
    										while ($mod=mysqli_fetch_array($modelos)) { ?>
        									<option value="<?php echo $mod["idmodelo"]; ?>" <?php  if ($reserva["idmodelo"] == $mod["idmodelo"]) { echo "selected"; } ?> ><?php echo $mod["modelo"]; ?></option>

										 <?php } ?>

									</select>
								</div>
							</div>

							<div id="lineados">


								<div id="color" style="width:20%;">
									<label>Color:</label>
									<input type="text" name="color" id="color"  value="<?php echo $reserva["color"] ?>" style="width:70%;" disabled >
								</div>

								<div id="altuno" style="width:20%;">
									<label>Alt 1:</label>
									<input type="text" name="altuno" id="altuno" value="<?php echo $reserva["altuno"] ?>" style="width:70%;" disabled  >
								</div>
								<div id="altdos" style="width:20%;">
									<label>Alt 2:</label>
									<input type="text" name="altdos" id="altdos" value="<?php echo $reserva["altdos"] ?>" style="width:70%;" disabled >
								</div>
								<div id="interno" style="width:16%;">
									<label>Interno:</label>
									<input type="text" name="interno" id="interno" value="<?php echo $reserva["interno"] ?>" style="width:50%;" readonly="readonly" >
									<input type="hidden" name="internodos" id="internodos" value="<?php echo $reserva["interno"] ?>" >
								</div>
								<div id="nroorden" style="width:22%;">
									<label>Nro Orden:</label>
									<input type="text" name="nroorden" id="nroorden"  value="<?php echo $reserva["nroorden"] ?>" style="width:60%;" readonly="readonly">
									<input type="hidden" name="nroordendos" id="nroordendos"  value="<?php echo $reserva["nroorden"] ?>">
								</div>
							</div>

						</div>
						<div id="usado">

							<div id="lineauno">

								<div id="internou" style="width:25%; margin-left: 25px;">
									<label>Interno:</label>
									<input type="text" name="internou" id="internou"  style="width:50%;" value="<?php echo $reserva["internou"] ?>" disabled>
								</div>

								<div id="Detalleu" style="width:70%;">
									<label>Detalle:</label>
									<input type="text" name="detalleu" id="detalleu"  size="70" value="<?php echo $reserva["detalleu"]; ?>" disabled>
								</div>


							</div>

							<div id="lineados">


								<div id="coloru" style="width:33%; margin-left: 35px;">
									<label>Color:</label>
									<input type="text" name="colorusa" id="colorusa"  style="width:50%;" value="<?php echo $reserva["coloru"]; ?>" disabled >
								</div>


								<div id="aniou" style="width:33%;">
									<label>A&ntilde;o:</label>
									<input type="text" name="aniousa" id="aniousa" style="width:30%;" value="<?php echo $reserva["aniou"]; ?>" disabled>
								</div>

								<div id="dominiou" style="width:20%;">
									<label>Dominio:</label>
									<input type="text" name="dominiou" id="dominiou" style="width:50%; text-transform:uppercase;" value="<?php echo $reserva["dominiou"] ?>" disabled>
								</div>
							</div>
						</div>
					</div>

				</fieldset>
</div>

<div id="detalle">
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
			<?php include("../includes/facturacion/detalle.php") ?>
		</div>
	</fieldset>
</div>

<fieldset>
	<div id="entrega_usado">
		<div class="fila">

					<div style="width: 22%">
						<label>Marca:</label>
						<input type="text" id="marcau" name="marcau" size="13" value="<?php echo $usadoe['marca']; ?>" disabled >
					</div>


					<div style="width: 21%; text-align: center;">
						<label>Tipo:</label>
						<input type="text" id="tipou" name="tipou" size="13" value="<?php echo $usadoe['tipo']; ?>" disabled>
					</div>

					<div style="width: 54%; float:right !important;">
						<label>Modelo:</label>
						<input type="text" id="modelou" name="modelou"  size="50" value="<?php echo $usadoe['modelo']; ?>" disabled>

					</div>

					</div>


					<div class="fila">

					<div style="width: 20%">
						<label>Color:</label>
						<input type="text" id="coloru" name="coloru" size="10" value="<?php echo $usadoe['color']; ?>" disabled>
					</div>


					<div style="width: 16%; text-align: center;">
						<label>A&ntilde;o:</label>
						<input type="text" id="aniou" name="aniou" size="2" maxlength="4" value="<?php echo $usadoe['anio']; ?>" disabled>
					</div>

					<div style="width: 20%; text-align: center; ">
						<label>Dominio:</label>
						<input style="text-transform:uppercase;" type="text" id="dominio" name="dominio" value="<?php echo $usadoe['dominio']; ?>" size="5" maxlength="7" disabled >
					</div>

					<div style="width: 20%; text-align: center;">
						<label>KM:</label>
						<input type="text" id="km" name="km" size="10" value="<?php echo $usadoe['km']; ?>" disabled>
					</div>

					<div style="width: 20%; text-align: center; float:right !important;" >
						<label>Info:</label>
						<input type="text" id="info" name="info" size="10"  value="<?php echo $usadoe['info'];?>" disabled>
					</div>



				</div>



				</div>
</fieldset>


<fieldset>
<div id="observacion">
	<textarea id="observacion" name="observacion" rows="4" style="width: 97%; font-size:1em;" ><?php echo $factura['observacion'] ?></textarea>
</div>
</fieldset>

<div id="btn_gdr">
	<input type="submit" name="guardar" value="Guardar Detalle" size="30">
	<input type="submit" id="enviar_res" name="enviar_res" style="float:right; color:red;" value="<?php if ($factura['estado']==0) { echo "Solicitar Facturaci&oacute;n"; }else { echo "Volver a Solicitar Facturaci&oacute;n"; } ?>" size="30">
</div>

</form>

	</div>
</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
