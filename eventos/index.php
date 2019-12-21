<?php


@session_start();

//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
	//si no existe, envio a la página de autentificacion
	header("Location: ../index.php");
	//ademas salgo de este script
	exit();
}

	include("funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");


 ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Lista de Eventos</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="icon" type="image/png" href="imgcomunes/logopest.png"/>
	<link rel="stylesheet" href="css/styles.css">
	<link rel="stylesheet" href="css/estilo_default.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

	<script>
		$(document).ready(function(){

			$("#nuevo_evento").click(function(event){
				event.preventDefault();
				operacion="alta_evento";
				$.ajax({
					url:"evento_abm.php",
					cache:false,
					type:"POST",
					data:{operacion:operacion},
					success:function(result){
						self.location = "evento.php?id="+result;
					}
		    	});

			})
	//----------------------------------------------------------------------------

			$("#btn_buscar").click(function(){
				tipo= $("#tipo").val();
				$.ajax({
					url:"eventos_buscar.php",
					cache:false,
					type:"POST",
					data:{tipo:tipo},
					success:function(result){
						$("#zona_tabla_act").html(result);
					}
		    	});

			})

		});
	</script>

</head>
<body class="desarroll">


	<div class="ed-container ">
		<div class="ed-item centrar-texto">
			<h1>Lista de Eventos</h1>
			<hr>
		</div>

	</div>

	<div class="ed-container web-70 zona-nav">
		<div class="ed-item web-80">

			<?php
			$SQL="SELECT * FROM eventos WHERE activo=1 AND not isnull(titulo) ORDER BY id_evento DESC LIMIT 100";
			$eventos = mysqli_query($con, $SQL);
			?>

			<select name="tipo" id="tipo">
				<option value="">Todos los Eventos</option>
				<option value="Campo">Campo</option>
				<option value="Eventos internos">Eventos internos</option>
				<option value="Lanzamiento de Producto">Lanzamiento de Producto</option>
				<option value="Promociones Plan de Ahorro">Promociones Plan de Ahorro</option>
				<option value="Promociones Convencional">Promociones Convencional</option>
				<option value="Promociones MF">Promociones MF</option>
			</select>
			<input type="button" value="Buscar" id="btn_buscar">
		</div>
		<div class="ed-item web-20 derecha-contenido">
			<a class="icon-enlace espacio nuevo_evento" id="nuevo_evento" data-mov="1" href="evento.php">Nuevo Evento</a>
		</div>
	</div>




	<div class="ed-container">
		<div class="ed-item" >

			<div class="zona-tabla-80" id="zona_tabla_act">

				<table class="tabla-default">
					<thead>
						<tr>
							<td width="2%">Nro</td>
							<td width="20%">Evento</td>
							<td width="7%">Fec. Inicio</td>
							<td width="7%">Fec. Fin</td>
							<td width="10%">Origen</td>
							<td width="10%">Ubicación</td>
							<td width="2%">Opción</td>
						</tr>
					</thead>
					<tbody>
						<?php
							while ($evento=mysqli_fetch_array($eventos)) { ?>
							<tr>
								<td><div class="centrar-texto"><?php echo $evento["nro"]; ?></td></div>
								<td><?php echo $evento["titulo"];?></td>
								<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($evento["fecha_inicio"]);?></div>	</td>
								<td><div class="centrar-texto"><?php echo cambiarFormatoFecha($evento["fecha_fin"]);?></div> </td>
								<td><div class="centrar-texto"><?php echo $evento["negocio"]; ?></div></td>
								<td><div class="centrar-texto"><?php echo $evento["ubicacion"]; ?></div></td>
								<td><div class="centrar-texto"><a class="icon-menu espacio" href="<?php echo "evento.php?id=".$evento["id_evento"]?>"></a></div></td>
							</tr>

							<?php } ?>

					</tbody>
				</table>

			</div>

		</div>

	</div>

	<div class="barra">

	</div>

<?php mysqli_close($con);	 ?>
</body>
</html>