<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	//$SQL="SELECT * FROM reservas WHERE idusuario =".$_POST['idusu']." AND anulada <> 1 AND entregada < 3 AND ORDER BY idreserva DESC";
	//$SQL = "SELECT reservas.*, clientes.nombre FROM reservas, clientes WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre <> '' AND clientes.nombre LIKE '%".$_POST["buscar"]."%'";
	$SQL="SELECT reservas.*, clientes.nombre FROM clientes INNER JOIN reservas ON clientes.idcliente = reservas.idcliente WHERE reservas.idusuario =".$_POST['idusu']." AND clientes.nombre LIKE '%".$_POST["buscar"]."%' AND entregada < 3 ORDER BY idreserva DESC";
	$res=mysqli_query($con, $SQL);
?>|

<script type="text/javascript">

$(document).ready(function(){

			$(".anular_reserva").click(function(event) {
			if (confirm("Seguro que deseas anular la operaci\u00f3n??")) {
				var id = $(this).attr('data-id'); //llamar a ajax anular la operacion y volver a la pagina asesores
				var obs = prompt("Ingrese Motivo por la cual anula la reserva.");

				if (obs!="" && obs != null) {
					document.location.href = "reserva_anular.php?idres=" + id + "&obser=" + obs + "&";
					return false;
				};
			};
		});

			$(".facturar").click(function(event) {

			var id = $(this).attr('data-id');
			$.ajax({
				url:"facturacion_cargar.php",
				cache:false,
				type:"POST",
				data:{idres:id},
				success:function(result){
					document.location.href ="facturacion.php?IDrecord="+id;
				}
			})
		});

	});

   </script>

<?php include("asesor_cuerpo.php");
 mysqli_close($con); ?>
