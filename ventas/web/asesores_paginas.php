<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	$SQL= "SELECT
		reservas.*, clientes.nombre
		FROM
		clientes
		INNER JOIN
		reservas ON clientes.idcliente = reservas.idcliente
		WHERE
	 	not isnull(compra) AND 	reservas.idusuario = ".$_POST['idusu']." AND entregada < 3
	 	ORDER BY fecres DESC, idreserva DESC
	 	LIMIT ".$_POST["inicio"]." , 15";
	$res=mysqli_query($con, $SQL);
?>

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
