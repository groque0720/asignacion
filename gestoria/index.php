<?php
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	@session_start();
	//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
	if ($_SESSION["autentificado"] != "SI") {
		//si no existe, envio a la página de autentificacion
		header("Location: ../login");
		//ademas salgo de este script
		exit();
	}else{
		$id_sucursal=$_SESSION["idsuc"];
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Gestoría</title>
	<link rel="shortcut icon" type="image/x-icon" href="dyv.ico" />
	<link rel="stylesheet" href="css/estilo.css">
	<!-- <link href="https://file.myfontastic.com/PKG4Yur63nr52FU8DsbmDY/icons.css" rel="stylesheet"> -->
	<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
	<script src="js/index.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />
	<script src="alertas_query/sweetalert-dev.js"></script>
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="stylesheet" href="css/estilo_proceso.css">
</head>
<body>

	<section class="mod model-3">
	  <div class="spinner">
	  	<img class="imagen_gira" src="imagenes/logo_dyv.png" alt="">

	  </div>
	</section>
	<header>
		<div class="zona-header input-90">

			<div class="zona-logo-dyv">
				<img class="logo-dyv" src="imagenes/logodyv.png" alt="DyV">
			</div>
			<div class="zona-logo-tyt">
				<img class="logo-toyota" src="imagenes/logo_toyota.png" alt="Toyota">
			</div>

		</div>
	</header>
	<div class="contenedor-lista">
		<div class="zona-cabecera ">
			<div class="zona-menu">
				<ul class="menu">
					<li class="item-menu"><a class="icon-plus item__link cuadro" id="btn_nuevo" href="nuevo_registro.php">Nuevo</a></li>
				</ul>

			</div>

			<div class="zona-filtro">

				<form action="" class="form_filtro" method="post">
					<label for="">Sucursal</label>
					<select  id="idsucursal" name="idsucursal">
						<option value="0">Todos</option>
						<?php
							$SQL="SELECT * FROM sucursales";
							$res=mysqli_query($con, $SQL);
							;
							while ($suc=mysqli_fetch_array($res)) { ?>
								<option value="<?php echo  $suc['idsucursal'];?>"><?php echo $suc['sucursal'];?></option>
							<?php } ?>
					</select>
					<label for="">Filtrar</label>
					<input type="date" id="fecha_desde" name="fecha_desde" class="input_fecha_filtro" value="<?php echo primer_dia_mes(); ?>">
					<label for="">hasta</label>
					<input type="date" id="fecha_hasta" name="fecha_hasta" class="input_fecha_filtro" value="<?php echo ultimo_dia_mes(); ?>">
					<label for="">Estado</label>
					<select id="inscripto" name="inscripto">
						<option value="1" >Terminados</option>
						<option value="0">Pendientes</option>

					</select>
					<span class="icon-filter"> <input type="submit" class="buscar_fecha cuadro" value="Filtrar"></span>
				</form>
			</div>

			<div class='zona-activador'>
				<span class="icon-filter activar-filtro">Activar Filtro</span>
			</div>
			<div class="zona-buscar">
				<input type="text" name="buscar" id="buscar" class="input-buscar" placeholder="Buscar">
			</div>

		</div>
		<div class="contenido">

			<?php
				$SQL="SELECT * FROM view_registros_gestoria WHERE id_sucursal = $id_sucursal ORDER BY id_reg_gestoria DESC LIMIT 200";
				//$SQL="SELECT * FROM view_registros_gestoria ORDER BY id_reg_gestoria DESC";
				$res_reg = mysqli_query($con, $SQL);

				include('contenido_cuerpo.php');
			 ?>

		</div>
	</div>

	<footer>
		<div class='pie centrar-texto'> <img class="logo-dyv" src="imagenes/logodyv.png" alt="DyV"></div>
	</footer>
</body>

</html>
