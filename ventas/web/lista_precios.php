<!DOCTYPE html>
<html lang="es">
<head>
    <title>Operaciones Activas</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_info_p.css">
<link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>

    <script type="text/javascript">

$(document).ready(function(){

	$("#dialog").dialog({
		autoOpen: false,
		width: 600,
		height: 300,
		modal: true,
		buttons: {
			"Cerrar": function () {
			$(this).dialog("close");
				}
			}
		});

		$(".boton").click(function(event) {
			document.location.href ="reserva_alta.php";
			});

		$(".boton_b").click(function(event) {
			det=$("#buscar_c").val();
			idusuario =$("#idusu").val();
			$("#pagina").val(0);
		    $.ajax({url:"asesores_filtro.php",cache:false,type:"POST",data:{buscar:det, idusu:idusuario },success:function(result){
      		$("#cuerpo_asesor").html(result);
    		}});

			});

		$(".anular_r").click(function(event) {
			if (confirm("Seguro que deseas anular la operaci\u00f3n??")) {
				var id = $(this).attr('data-id'); //llamar a ajax anular la operacion y volver a la pagina asesores
				var obs = prompt("Ingrese Motivo por la cual anula la reserva.");

				if (obs!="" && obs != null) {
					document.location.href = "reserva_anular.php?idres=" + id + "&obser=" + obs + "&";
					return false;
				};
			};
		});

		$(".boton_s").click(function(event) {
			ini=parseInt($("#pagina").val())+10;
			$("#pagina").val(ini);
			idusuario =$("#idusu").val();
			$.ajax({url:"asesores_paginas.php",cache:false,type:"POST",data:{inicio:ini, idusu:idusuario},success:function(result){
	      	$("#cuerpo_asesor").html(result);

	    	}});
		});

		$(".boton_a").click(function(event) {
			ini=parseInt($("#pagina").val())-10;
			if (ini>=0) {
			$("#pagina").val(ini);
			idusuario =$("#idusu").val();
			$.ajax({url:"asesores_paginas.php",cache:false,type:"POST",data:{inicio:ini, idusu:idusuario},success:function(result){
	      	$("#cuerpo_asesor").html(result);
	    	}});}else{alert("No Hay Mas Registros Anteriores.")};
		});



		$(".boton").click(function(event) {
			document.location.href ="../web/asesores.php";
			});

	});
   </script>






 </head>

<body>

<div id="agrupar">

		<?php include("../includes/header.php") ?>

		<section id="seccion">
			<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
			<div class="fila">
				<div id="alta_sol" style="width: 10%; float: left;">
					<input type="button" class="boton" value="<-- Volver" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>


			</div>

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			//mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT * FROM reservas WHERE idusuario =".$_SESSION["id"]." AND anulada <> 1 AND entregada < 3 ORDER BY idreserva DESC";
			$res=mysqli_query($con, $SQL);
			 ?>


			<div id="cuerpo_asesor">
				<?php

			$SQL="SELECT * FROM grupos WHERE activo = 1 ORDER BY posicion";
			$grupos=mysqli_query($con, $SQL);

			while($grup=mysqli_fetch_array($grupos)) {
				echo "<strong>".$grup["grupo"]."</strong>";
			$SQL="SELECT * FROM modelos WHERE activo = 1 AND idgrupo =".$grup["idgrupo"]." ORDER BY posicion" ;
			$modelos=mysqli_query($con, $SQL);

			?>

			<table id="tabla" rules="all" border="1" style="width:100%;" >
				<tr style="text-align:center;background: #ccc;">
					<td width="29%">Modelos</td>
					<td width="9%">Flete-01</td>
					<td width="9%">Transf.-Insc.</td>
					<td width="9%">Neto</td>
					<td width="9%">IVA</td>
					<td width="10%">Subtotal</td>
					<td width="10%">Imp.Interno</td>
					<td width="12%">Precio Lista</td>

				</tr>

			<?php
				while($mod=mysqli_fetch_array($modelos)) {

					$SQL="SELECT * FROM listaprecio WHERE idmodelo = ".$mod['idmodelo']." AND activo = 1";
					$precios=mysqli_query($con, $SQL);
					$afectadas = mysql_num_rows($precios);
					if ($afectadas!=0) {
					$precio=mysqli_fetch_array($precios);
					?>

				<tr style="text-align: right;">
					<td style="text-align: left;"> <?php echo $mod["modelo"]?> </td>
					<td><?php echo number_format($precio["flete"], 2, ',','.')?></td>
					<td><?php echo number_format($precio["trans"], 2, ',','.')?></td>
					<td><?php if ($precio["neto"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["neto"], 2, ',','.');}?> </td>
					<td><?php if ($precio["iva"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["iva"], 2, ',','.');}?></td>
					<td><?php if ($precio["subtotal"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["subtotal"], 2, ',','.');}?></td>
					<td><?php if ($precio["impuesto"] == 0) {
									echo "-";
								}else{
									echo number_format($precio["impuesto"], 2, ',','.');}?></td>

					<td><?php echo $precio["moneda"]." ".number_format($precio["pl"], 2, ',','.')?></td>

				</tr>

				<?php }
			} ?>

			</table>

			<br>

			<?php } ?>
			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
