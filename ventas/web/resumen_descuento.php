<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title></title>
	    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_info_p.css">
	<link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>

  	<script type="text/javascript">

	  	$(document).ready(function(){

		  	$("#busq").click(function(event) {

		  		if ($("#fecha_buscar").val()!=null || $("#fecha_buscar").val()!=''  ) {

					det=$("#fecha_buscar").val();
					$.ajax({url:"resumen_descuento_buscar.php",cache:false,type:"POST",data:{abuscar:det },success:function(result){
						$("#zona_actualizar").html(result);
					}});
				}else{ alert("Ingresar Fecha")};

			});

		  });
  	</script>

</head>
<body>
	<strong>Buscar descuento desde:</strong>
	<input type="date" id="fecha_buscar" >
	<input type="button" id="busq" class="busq" value="buscar" style="background:#D8F781; color:#000; padding: 5px; border-radius: 5px; margin-left: 20px;"/>


	<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");


	$SQL="SELECT
	lineas_detalle.monto as monto,
	reservas.idreserva,
	clientes.nombre AS cliente,
	reservas.idtipo,
	reservas.idgrupo,
	reservas.idmodelo,
	reservas.compra AS compra,
	reservas.detalleu AS detalleu,
	usuarios.nombre AS asesor,
	reservas.fecres,
	reservas.enviada AS enviada,
	reservas.idcliente as idcliente
	FROM
	reservas
	Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
	Inner Join clientes ON clientes.idcliente = reservas.idcliente
	Inner Join usuarios ON usuarios.idusuario = reservas.idusuario
	WHERE
	lineas_detalle.monto <  '0' AND
	fecres >= CURDATE() AND not isnull(compra) AND anulada <> 1  ORDER BY idreserva";
	$res=mysqli_query($con, $SQL);
	 ?>

	<div id="zona_actualizar">
	 <?php include("control_reservas_cuerpo.php") ?>
	</div>

	<header>
	</header>
	<nav>
	</nav>
	<section >
		<div class="realizadas">

			<table>
				<thead>
				</thead>
				<tbody>
				</tbody>
			</table>



		</div>


	</section>
	<footer>
	</footer>
</body>
<?php mysqli_close($con); ?>
</html>