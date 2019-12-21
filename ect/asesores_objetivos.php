
<?php

	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT * FROM ect_asesores WHERE objetivo_activo = 1";
	$asesores=mysqli_query($con, $SQL);

	while ($asesor=mysqli_fetch_array($asesores)) {

		$SQL="SELECT * FROM ect_tipos_objetivos WHERE borrar = 0 ORDER BY posicion";
		$objetivos=mysqli_query($con, $SQL);

		while ($objetivo = mysqli_fetch_array($objetivos)) {
			
			$SQL="SELECT * FROM ect_asesores_r_objetivos WHERE id_asesor_ect = ".$asesor['id']." AND id_tipo_objetivo = ".$objetivo['id'];
			$asesores_objetivos = mysqli_query($con, $SQL);

			$cant = mysqli_num_rows($asesores_objetivos);

			if ($cant == 0 ) {

				$SQL="INSERT INTO ect_asesores_r_objetivos (id_tipo_objetivo, id_asesor_ect) VALUES ({$objetivo['id']}, {$asesor['id']})";
				mysqli_query($con, $SQL);
				
			}

		}

 	} ?>


<div class="titulo-modelo">
	<?php echo "ASESORES POR OBJETIVOS"; ?>
</div>
<?php include('asesores_objetivos_cuerpo.php'); ?>
