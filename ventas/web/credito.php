<!DOCTYPE html>
<html lang="es">
<head>
    <title>Area Créditos</title>
   	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>


 	<script type="text/javascript">

 	$(document).ready(function(){

 		$("#cargaestado").hide();

 		$('.eliminar_f').click(function(event) {
 			event.preventDefault();
 		if (confirm("Seguro que deseas borrar la fila??")) {
		id = $(this).attr('data-id');
		nrocred= $("#idcredito").val();

		$.ajax({url:"credito_eliminar_filas.php",cache:false,type:"POST",data:{idfila:id, idcredito: nrocred},success:function(result){
      		$("#act_ajax").html(result);
    		}});
 		 event.preventDefault();
 		};
		});

 		$('.editar_f').click(function(event) {
 			event.preventDefault();
	  		var obs = prompt("Escriba la Observacion a Editar");
				if (obs!="" && obs != null) {
					id = $(this).attr('data-id');
					nrocred= $("#idcredito").val();

					$.ajax({url:"credito_editar_filas.php",cache:false,type:"POST",data:{idfila:id, idcredito: nrocred, ob:obs},success:function(result){
      				$("#act_ajax").html(result);
    				}});

				};
	  	});

 		// controlo los perfiles del asesor de credito y los demas (codigo ==11 es el asesor de credito)
 		if ($("#idperfil").val()==11) {
 			$("#cargaestado").show();
 			$("#escredito").show();
 			$("#nocredito").hide();
 		}else{
 			$("#nocredito").show();
 			$("#escredito").hide();
 			$("table tr td:last-child").css({ // oculto la ultima columna de las observaciones
   				"display": "none"
				})

 		};

 		$("#agregar_l").click(function(){
 			if (($("#fecha").val() == 0)||($("#estado").val() == 0)) {
 				alert("Ingrese como minimo la Fecha y Estado");
 			}else{;
			fec=$("#fecha").val();
		    est=$("#estado").val();
		    obs=$("#observacion").val();
		    monto_p=$("#monto_pago").val();
		    nrocred=$("#idcredito").val();
		    idres=$("#idreserva").val();
		    liq=$("#estado").val();
		    $.ajax({url:"credito_insertar_filas.php",cache:false,type:"POST",
		 data:{fecha:fec, estado:est, observacion:obs, idcredito:nrocred, idres: idres, monto: monto_pago, cod_liq: liq},success:function(result){
      		$("#act_ajax").html(result);
    		}});
			$("#fecha").val('');
    		$("#estado").val('');
    		$("#observacion").val('');
    		$("#fecha").focus();};
    		 		});

 		$("form").keypress(function(e) {
       		if (e.which == 13) {



            }
    	});

 		$(".boton").click(function(event) {
			document.location.href ="creditos.php";
			});

 		$("#boton").click(function(){
	  	$( "#dialogo" ).dialog("open");
	  	 });

	  	$( "#dialogo" ).dialog({
     	autoOpen: false, // no abrir automáticamente
     	resizable: true, //permite cambiar el tamaño
     	width: 430,
     	height:370, // altura
	    modal: true, //capa principal, fondo opaco
	    buttons: { //crear botón de cerrar
	    "Confirmar": function() {
    	if (($("#fecha").val() == 0)||($("#estado").val() == 0)) {
 				alert("Ingrese como minimo la Fecha y Estado");
 			}else{

 			opt=$("#estado option:selected").text();
 			idusu=$("#idusuario").val();
 			idres=$("#idreserva").val();
 			mon=$("#monto").val();
 			ase=$("#asesor").val();
 			cli=$("#cliente").val();
			fec=$("#fecha").val();
		    est=$("#estado").val();
		    obs=$("#observacion").val();
		    nrocred=$("#idcredito").val();
		    monto_p=$("#monto_pago").val();
		    financiera=$("#idfinanciera").val();
		    $.ajax({url:"credito_insertar_filas.php",cache:false,type:"POST",data:{idusu:idusu, fin:financiera, monto_p: monto_p, opcion:opt, monto:mon, asesor: ase, cliente: cli, idres:idres, fecha:fec, estado:est, observacion:obs, idcredito:nrocred},success:function(result){
      		$("#act_ajax").html(result);
    		}});
			$("#fecha").val('');
    		$("#estado").val('');
    		$("#observacion").val('');
    		$("#fecha").focus();
    	$( this ).dialog( "close" )};
   			},
        "Cerrar": function() {
          $( this ).dialog( "close" );
        	}
          }
	    });

		$("#cred_liq").hide();

		$("#estado").change(function()
  		 {
  		 	if ("70"==$(this).val()) {
  		 		$("#cred_liq").show(300);
  		 	}else{
  		 		$("#cred_liq").hide(300);
  		 	}

  		  });

 	});


 	</script>

 </head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>

		<?php

		include("../funciones/func_mysql.php");
		conectar();
		//mysql_query("SET NAMES 'utf8'");




		$SQL="SELECT
				reservas.idreserva AS idreserva,
				reservas.compra AS compra,
				reservas.idusuario AS idusuario,
				usuarios.nombre AS asesor,
				clientes.nombre AS cliente,
				reservas.fecres AS fecres,
				reservas.detalleu AS detalleu,
				creditos.estado AS estado,
				lineas_detalle.credito AS credito,
				reservas.idcredito AS idcredito,
				tipos_creditos.tipocredito AS tipocredito,
				financieras.financiera AS financiera,
				financieras.idfinanciera AS idfinanciera,
				lineas_detalle.monto AS monto
				FROM
				reservas
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				Inner Join creditos ON creditos.idcredito = reservas.idcredito
				Inner Join lineas_detalle ON lineas_detalle.idreserva = reservas.idreserva
				Inner Join codigos ON lineas_detalle.idcodigo = codigos.idcodigo
				Inner Join tipos_creditos ON tipos_creditos.idtipocredito = codigos.tipocredito
				Inner Join financieras ON financieras.idfinanciera = codigos.financiera
				WHERE
				clientes.nombre IS NOT NULL  AND
				reservas.anulada <>  '1' AND
				lineas_detalle.credito =  '1'
				 AND reservas.idcredito=".$_GET["IDrecord"];
				$res=mysqli_query($con, $SQL);
				$credito=mysqli_fetch_array($res);


		 ?>

		<div id="alta_sol">
			<div id="nocredito">
			<a href="javascript:window.history.back();">&laquo; Volver atrás</a>
			</div>
			<div id="escredito">
			<input type="button" class="boton" value="<- Volver" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
			</div>
		</div>
		<section id="seccion">

			<div class="fila">
				<div  style="width: 100%; float: left; text-align:center">
					<span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Detalle de Cr&eacute;dito</span>
				</div>
				<input type="hidden" id="idcredito" name="idcredito" value="<?php echo $credito["idcredito"]; ?>">
				<input type="hidden" id="idreserva" name="idreserva" value="<?php echo $credito["idreserva"]; ?>">
				<input type="hidden" id="idusuario" name="idusuario" value="<?php echo $credito["idusuario"]; ?>">
			</div>
			<hr>

			<div id="cuerpo_asesor">

				<div>


				<div class="fila">
						<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
						<input id="idperfil" name="idperfil" type="hidden" value="<?php echo $_SESSION["idperfil"]; ?>">
					<div  style="width: 50%; float: left;">
						<label>Cliente:</label>
						<input style="text" id="cliente" name="cliente" value="<?php echo $credito["cliente"]; ?>" size="40">
					</div>

					<div  style="width: 50%; float: right; text-align:center;">
						<label>Asesor:</label>
						<input style="text" id="asesor" name="asesor" value="<?php echo $credito["asesor"]; ?>" size="40">
					</div>

				</div>

				<div class="fila">

					<div  style="width: 34%; float: left;">
						<label>Tipo Cr&eacute;dito:</label>
						<input style="text" id="cliente" name="cliente" value="<?php echo $credito["tipocredito"]; ?>" size="15">
					</div>

					<div  style="width: 34%; float: left; text-align:center;">
						<label>Financiera:</label>
						<input style="text" id="financiera" name="financiera" value="<?php echo $credito["financiera"]; ?>" size="15">
						<input type="hidden" id="idfinanciera" name="idfinanciera" value="<?php echo $credito["idfinanciera"]; ?>">
					</div>

					<div  style="width: 30%; float: left;">
						<label>Monto a Financiar:</label>
						<input style="text" id="monto" name="monto" value="<?php echo number_format($credito['monto'], 2, ',','.'); ?>" size="15">
					</div>

				</div>

				<hr>



			<div  id="cargaestado" class="fila" style="margin-bottom: 10px;">

			<input type="button" id="boton" value="Nuevo Registro de Cr&eacute;dito">

			<div id="dialogo" title="Registro de Cr&eacute;dito">
					<div id="cargar_det" style="width:90%; float:left; margin-left:10px;">
						<input type="date" id="fecha" name="fecha">
						<select id="estado" name="estado" >
							<option value="0"></option>
							<option value="1">Recibido</option>
							<option value="2">Enviado</option>
							<option value="22">En An&aacute;lisis</option>
							<option value="3">Observado</option>
							<option value="4">Rechazado</option>
							<option value="5">Pre-Aprobado</option>
							<option value="66">Aprobado Obs</option>
							<option value="6">Aprobado</option>
							<option value="70">Liquidado</option>
						</select>

						<div id="cred_liq" style="margin: 5px;">
							<hr>
							<label style="text-align: center; margin-left: 50px;">Monto Liquidado</label>
							<input type="text" id="monto_pago" name="monto_pago" value="0" size="12" style="margin-left: 8px; text-align:right;">
							<hr>
						</div>
							<hr>
						<textarea name="observacion" id="observacion" placeholder="Observaci&oacute;n" cols="32" rows="7"></textarea>

					</div>


			</div>

		</div>

			</div>
				<hr>

				<div id="act_ajax">

					<table rules="all" border="1" style="width: 100%;">
						<thead>
							<tr>
								<td width="10%">Fecha</td>
								<td width="10%">Estado</td>
								<td width="68%">Observaci&oacute;n</td>
								<td width="7%">Editar</td>
							</tr>
						</thead>

						<tbody>

								<?php
								$SQL="SELECT * FROM creditos_lineas WHERE idcredito =".$_GET["IDrecord"];
								$lineas_creditos= mysqli_query($con, $SQL);

								while ($lineas=mysqli_fetch_array($lineas_creditos)) { ?>
								<tr>
								<td><?php echo cambiarformatofecha($lineas["fecha"]); ?> </td>
								<td>
									<select id="estado_l" name="estado_l" disabled>
										<option value="0"></option>
										<option value="1" <?php if ($lineas['estado']==1) {  echo "selected";} ?>>Recibido</option>
										<option value="2" <?php if ($lineas['estado']==2) {  echo "selected";} ?>>Enviado</option>
										<option value="22" <?php if ($lineas['estado']==22) {  echo "selected";} ?>>En An&aacute;lisis</option>
										<option value="3" <?php if ($lineas['estado']==3) {  echo "selected";} ?>>Observado</option>
										<option value="4" <?php if ($lineas['estado']==4) {  echo "selected";} ?>>Rechazado</option>
										<option value="5" <?php if ($lineas['estado']==5) {  echo "selected";} ?>>Pre-Aprobado</option>
										<option value="6" <?php if ($lineas['estado']==6) {  echo "selected";} ?>>Aprobado</option>
										<option value="66" <?php if ($lineas['estado']==66) {  echo "selected";} ?>>Aprobado Obs</option>
										<option value="70" <?php if ($lineas['estado']==7 || $lineas['estado']==70) {  echo "selected";} ?>>Liquidado</option>
									</select>
								</td>
								<td><?php echo $lineas['observacion'] ?> </td>
								<td>
									<a class="editar_f" href="" data-id="<?php echo $lineas["idcreditolinea"];?>"><img src="../imagenes/editar.png"  width="20px"></a>
									<a class="eliminar_f" href="" data-id="<?php echo $lineas["idcreditolinea"];?>"><img src="../imagenes/eliminar.png"  width="20px"></a>
								</td>

								</tr>
								<?php } ?>



						</tbody>

					</table>

				</div>



			</div>

		</section>


	</div>
</body>
<?php  mysqli_close($con);  ?>
</html>