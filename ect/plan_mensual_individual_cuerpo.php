
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

?>

	<div class="flexible justificar titulo-impresion">

		<div class="ancho-20">
			<div class="zona-logo-ppal">
				<img class="logo-ppal" src="imagenes/logodyv_c.png" alt="">
			</div>
		</div>

		<div class="ancho-50 negrita centrar-texto titulo-central" style="font-size: 25px;">
			PLAN MENSUAL INDIVIDUAL
		</div>

		<div class="ancho-20 derecha-texto">

				<?php 

					$SQL="SELECT * FROM ect_view_asesores_activos WHERE id_asesor_ect = ".$id_asesor;
					$usuarios = mysqli_query($con, $SQL);

					$usuario = mysqli_fetch_array($usuarios);


					$SQL="SELECT * FROM meses WHERE idmes = ".$mes;
					$meses = mysqli_query($con, $SQL);

					$nombre_mes=mysqli_fetch_array($meses);
				 ?>


				<div style="font-size: 20px;" class="negrita derecha-texto nom-usu">
					<?php echo $usuario['asesor']; ?>
				</div>

				<div class="derecha-texto mes-pmi">
					<?php echo $nombre_mes['mes'].' ' .$ano; ?>
				</div>

		</div>

		
	</div>



<?php
echo "<hr class='margen-arriba-10'>";
// echo "<hr>"; 
	include('plan_mensual_individual_cuerpo_ventas.php');
echo "<hr>";
	include('plan_mensual_individual_cuerpo_contactos_td.php');
echo "<hr>";
	include('plan_mensual_individual_cuerpo_ventas_mensuales.php');
echo "<hr>";
	include('plan_mensual_individual_cuerpo_devolucion.php');

 ?>

