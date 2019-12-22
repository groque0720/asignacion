<!DOCTYPE html>
<html lang="es">
<head>
    <title>Area Créditos</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
     <link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>

<script type="text/javascript">

	$(document).ready(function(){

		$(".boton_b").click(function(event) {
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
			$.ajax({url:"creditos_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});
		});

		$(".boton_s").click(function(event) {
			ini=parseInt($("#pagina").val())+15;
			$("#pagina").val(ini);
			$.ajax({url:"creditos_paginas.php",cache:false,type:"POST",data:{inicio:ini},success:function(result){
	      	$("#cuerpo_asesor").html(result);

	    	}});
		});

		$(".boton_a").click(function(event) {
			ini=parseInt($("#pagina").val())-15;
			if (ini>=0) {
			$("#pagina").val(ini);
			$.ajax({url:"creditos_paginas.php",cache:false,type:"POST",data:{inicio:ini},success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});}else{alert("No Hay Mas Registros Anteriores.")};
		});

	});
</script>

</head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>


		<section id="seccion">

			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="alta_sol" style="width: 40%; float: left;">
					<span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Lista de Cr&eacute;ditos activos</span>
				</div>
				<div id="alta_sol" style="width: 30%; float: right;">
					<input type="text" id="buscar_c" name="buscar_c" placeholder="Buscar">
					<input type="button" class="boton_b" value="Buscar" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>

			</div>

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			//mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT
				reservas.idreserva AS idreserva,
				reservas.fecres AS fecres,
				reservas.compra AS compra,
				clientes.nombre AS cliente,
				usuarios.nombre AS asesor,
				tipos_creditos.tipocredito AS credito,
				financieras.financiera AS financiera,
				lineas_detalle.monto AS monto,
				reservas.detalleu AS detalleu,
				reservas.idgrupo,
				reservas.idmodelo,
				reservas.idcredito  AS idcredito
				FROM
				reservas
				Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
				Inner Join codigos ON lineas_detalle.idcodigo = codigos.idcodigo
				Inner Join tipos_creditos ON codigos.tipocredito = tipos_creditos.idtipocredito
				Inner Join financieras ON codigos.financiera = financieras.idfinanciera
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				WHERE
				codigos.credito =  '1' AND
				reservas.anulada =  '0'
				ORDER BY
				idreserva DESC LIMIT 15";
			$res=mysqli_query($con, $SQL);

			 ?>
			 <hr>

				<div class="fila" style="margin: 10px;">
					<div style="float: left; width: 45%;">
						<input type="button" class="boton_a" value="&#60;&#60; Anteriores" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
					<div style="float: right; width: 45%; text-align: right; margin-right: 20px;">
						<input type="button" class="boton_s" value="Siguientes &#62;&#62;" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
				</div>
			<div id="cuerpo_asesor">


				<?php include("credito_cuerpo.php"); ?>
			</div>
		</section>

	</div>

</body>
</html>
