<!DOCTYPE html>
<html lang="es">
<head>
    <title></title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_info_p.css">
    <link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		$(".boton_b").click(function(event) {
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
			$.ajax({url:"administracion_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});
		});

		$(".boton_s").click(function(event) {
			ini=parseInt($("#pagina").val())+15;
			$("#pagina").val(ini);
			$("#buscar_c").val('');
			idusuario =$("#idusu").val();
			$.ajax({url:"administracion_paginas.php",cache:false,type:"POST",data:{inicio:ini, idusu:idusuario},success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});
		});

		$(".boton_a").click(function(event) {
			ini=parseInt($("#pagina").val())-15;
			if (ini>=0) {
			$("#pagina").val(ini);
			$("#buscar_c").val('');
			idusuario =$("#idusu").val();
			$.ajax({url:"administracion_paginas.php",cache:false,type:"POST",data:{inicio:ini, idusu:idusuario},success:function(result){
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

				<div id="alta_sol" style="width: 30%; float: left;">
					<button type="button"> <a href="control_pagos_clientes.php">Control de Pagos Gral</a> </button>
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
			$SQL="SELECT reservas.*, clientes.nombre FROM clientes INNER JOIN reservas ON clientes.idcliente = reservas.idcliente WHERE
	  		reservas.anulada = 0 AND reservas.enviada >= 1  ORDER BY clientes.nombre  LIMIT 15";
			$res=mysqli_query($con, $SQL);
			 ?>
			 <hr>

			 <div class="fila flechas" style="margin: 10px;">
					<div style="float: left; width: 45%;">
						<input type="button" class="boton_a" value="&#60;&#60; Anteriores" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
					<div style="float: right; width: 45%; text-align: right; margin-right: 20px;">
						<input type="button" class="boton_s" value="Siguientes &#62;&#62;" style="background:Red; color:#fff; padding: 5px; border-radius: 5px;"/>
					</div>
				</div>

			<div id="cuerpo_asesor">
				<?php include("administracion_cuerpo.php") ?>
				<span>Registros desde 1 hasta 15  </span>
			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
