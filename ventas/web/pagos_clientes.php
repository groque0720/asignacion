<!DOCTYPE html>
<html lang="es">
<head>
    <title>Area Control de Pagos</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>


<script type="text/javascript">

	$(document).ready(function(){

		$("#buscar_c").keypress(function(e){
       var keycode = (event.keyCode ? event.keyCode : event.which);
       		det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
			$.ajax({url:"pagos_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});

        	});

		$(".boton_b").click(function(event) {
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
			$.ajax({url:"pagos_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});
		});

		$(".boton_s").click(function(event) {
			ini=parseInt($("#pagina").val())+15;
			idsuc=$("#idsuc").val();
			$("#pagina").val(ini);
			$.ajax({url:"pagos_paginas.php",cache:false,type:"POST",data:{inicio:ini, idsuc:idsuc},success:function(result){
	      	$("#cuerpo_asesor").html(result);

	    	}});
		});

		$(".boton_a").click(function(event) {
			ini=parseInt($("#pagina").val())-15;
			idsuc=$("#idsuc").val();
			if (ini>=0) {
			$("#pagina").val(ini);
			$.ajax({url:"pagos_paginas.php",cache:false,type:"POST",data:{inicio:ini, idsuc:idsuc},success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});}else{alert("No Hay Mas Registros Anteriores.")};
		});

	});
</script>

</head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>
		<input type="hidden" id="idsuc" value="<?php echo $_SESSION["idsuc"];?>">


		<section id="seccion">

			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="alta_sol" style="width: 30%; float: left;">
					<span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Lista - Clientes Activos</span>
				</div>
				<div id="alta_sol" style="width: 50%; float: right;">
					<input type="text" id="buscar_c" name="buscar_c" placeholder="Buscar" size="40">
					<input type="button" class="boton_b" value="Buscar" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>

			</div>

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			////mysql_query("SET NAMES 'utf8'");


			$SQL="SELECT
			reservas.idreserva AS idreserva,
			reservas.fecres AS fecres,
			reservas.compra AS compra,
			clientes.nombre AS cliente,
			usuarios.nombre AS asesor,
			reservas.detalleu AS detalleu,
			reservas.idcliente AS idcliente,
			reservas.idgrupo,
			reservas.idmodelo,
			reservas.idcredito,
			reservas.estadopago AS estadopago
			FROM
			reservas
			Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
			Inner Join clientes ON reservas.idcliente = clientes.idcliente
			WHERE
			reservas.anulada <> '1' AND
			reservas.enviada >=  '1' AND usuarios.idsucursal =".$_SESSION["idsuc"]." ORDER BY
			cliente ASC,
			asesor ASC LIMIT 15";
			$res=mysqli_query($con, $SQL);

			 ?>
			 <hr>

				<div class="fila" style="margin: 10px;">
					<div style="float: left; width: 45%;">
						<!-- <a href="javascript:window.history.back();">&laquo; Volver atrás</a> -->
						<input type="button" class="boton_a" value="&#60;&#60; Anteriores" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
					<div style="float: right; width: 45%; text-align: right; margin-right: 20px;">
						<input type="button" class="boton_s" value="Siguientes &#62;&#62;" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
				</div>
			<div id="cuerpo_asesor">


				<?php include("pagos_cliente_cuerpo.php"); ?>



			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
