<!DOCTYPE html>
<html lang="es">
<head>
    <title>Area Créditos</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">


<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

<script type="text/javascript">

	$(document).ready(function(){

		$('#buscar_c').autocomplete({
		source: "creditos_autocomplete.php"
		});

	var refreshId = setInterval(refrescarTablaEstadoSala, 7000);
	$.ajaxSetup({ cache: false });

	function refrescarTablaEstadoSala() {
	id=$("#idusu").val();
	$("#noti_credito").load('creditos_act_noti.php?id='+id, function(){});
	};

	//----------------------------------------------------------------------------------------------------
		 $("#buscar_c").keypress(function(e){

	       var keycode = (event.keyCode ? event.keyCode : event.which);
	      	if(keycode == '13'){

				det=$("#buscar_c").val();
				idusuario =$("#idusu").val();
				$("#pagina").val(0);
				$.ajax({url:"creditos_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
		      	$("#cuerpo_asesor").html(result);
		    	}});
	      	}
	 	});

		$(".boton_b").click(function(event) {
			event.preventDefault();
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
			$.ajax({url:"creditos_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});
		});

		$(".boton_s").click(function(event) {
			event.preventDefault();
			ini=parseInt($("#pagina").val())+15;
			$("#pagina").val(ini);
			$.ajax({url:"creditos_paginas.php",cache:false,type:"POST",data:{inicio:ini},success:function(result){
	      	$("#cuerpo_asesor").html(result);

	    	}});
		});

		$(".boton_a").click(function(event) {
			event.preventDefault();
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

		<?php
			include("../funciones/func_mysql.php");
			conectar();
			//mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario =".$_SESSION["id"]." AND visto=0 and borrar=0";
			$res=mysqli_query($con, $SQL);
			if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}

		?>
		<section id="seccion">

			<div class="fila">

				<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
				<input id="pagina" name="pagina" type="hidden" value="0">

				<div id="noti_credito" style="width: 30%; float: left; padding-top: 15px;">
					<a href="noticias.php" style="text-decoration:none; background:#D8F781; color:red; padding: 7px; border-radius: 5px; margin-left: 20px;" target="_blank">Notificaciones: <?php echo $cant_res['cantidad']; ?></a>
				</div>


				<div id="alta_sol" style="width: 60%; float: right;">
					<input type="text" id="buscar_c" name="buscar_c" placeholder="Buscar" size="50">
					<input type="button" class="boton_b" value="Buscar" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>

			</div>

			<?php


			$SQL="SELECT
				reservas.idreserva AS idreserva,
				reservas.compra AS compra,
				usuarios.nombre AS asesor,
				clientes.nombre AS cliente,
				reservas.fecres AS fecres,
				reservas.detalleu AS detalleu,
				reservas.idmodelo AS idmodelo,
				reservas.idgrupo AS idgrupo,
				creditos.estado AS estado,
				lineas_detalle.credito AS credito,
				reservas.idcredito AS idcredito,
				reservas.idfactura AS idfactura
				FROM
				reservas
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				Inner Join creditos ON creditos.idcredito = reservas.idcredito
				Inner Join lineas_detalle ON lineas_detalle.idreserva = reservas.idreserva
				WHERE
				clientes.nombre IS NOT NULL  AND
				reservas.anulada <>  '1' AND
				lineas_detalle.credito =  '1'
				ORDER BY
				estado ASC
				LIMIT 15";
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
<?php  mysqli_close($con);  ?>
</html>
