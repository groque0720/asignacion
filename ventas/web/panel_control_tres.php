<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title>Panel de Control</title>
	<link rel="stylesheet" href="../css/estilo_panel.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>


	<script type="text/javascript">

	$(document).ready(function(){

		$("#det_vtas").hide();

		$("#vta_ver_mod").click(function(event){
			$("#det_vtas").fadeIn();
		})

		$("#vta_ver_ase").click(function(event){
			$("#det_vtas").fadeOut();
		})

	})

	</script>
</head>
<body>

	<?php
		include("../funciones/func_mysql.php");
		conectar();
		mysql_query("SET NAMES 'utf8'");
		?>
	<div class="titulos">
		<h1>Derka y Vargas S. A.</h1>
	</div>

	<div class="sub_titulos">
		<h1>Reporte de Ventas</h1>
	</div>

	<div class="ventas">

		<?php
			$SQL="SELECT * FROM sucursales";
			$sucursales = mysqli_query($con, $SQL); ?>



			<?php

			while ($suc=mysqli_fetch_array($sucursales)) { ?>

			<div class="sucursal">

				<div class="det_suc">
					<?php echo $suc["sucursal"] ?>
				</div>



				<?php

				$nro = 1;

				$SQL="SELECT
				count(*) AS cantidad,reservas.fecres
				FROM
				reservas
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join sucursales ON usuarios.idsucursal = sucursales.idsucursal
				WHERE
				MONTH(reservas.fecres) =  MONTH(CURDATE())
				AND
				YEAR(reservas.fecres) =  YEAR(CURDATE())
				AND
				sucursales.idsucursal = ".$suc["idsucursal"];

				$sucs_cants=mysqli_query($con, $SQL);
				$suc_cant=mysqli_fetch_array($sucs_cants);


				 ?>

				<div class="tot_suc">
					<a href="#"><?php  echo $suc_cant["cantidad"]; ?></a>
				</div>

				<div class="criterios">
					<a href="#" id="vta_ver_mod" class="ver_vtas">Modelos</a>
					<a href="#" id="vta_ver_ase" class="ver_vtas">Asesores</a>
					<a href="#" id="vta_ver_tot" class="ver_vtas">Gral</a>
				</div>

				<table>
					<thead>
						<tr>
							<td width="7%">Nro x Suc</td>
							<td width="7%">Nro x Ase.</td>
							<td width="7%">Interno</td>
							<td width="7%">Compra</td>
							<td width="30%">Unidad</td>
							<td width="30%">Cliente</td>
						</tr>

					</thead>
				</table>

				<div class="det_suc_ase">

					<?php

					$SQL="SELECT idusuario,  nombre FROM usuarios WHERE idperfil = 3 AND idsucursal = ". $suc["idsucursal"];
					$ases_sucs=mysqli_query($con, $SQL);
					// $ase_suc=mysqli_fetch_array($ases_sucs);

					while ($ase_suc=mysqli_fetch_array($ases_sucs)) {

					echo $ase_suc["nombre"];
					echo "<br>";

					$SQL="SELECT
						reservas.fecres as fecres,
						reservas.compra as compra,
						reservas.idusuario as idusuario,
						clientes.nombre as cliente,
						reservas.idgrupo as grupo,
						reservas.idmodelo as modelo,
						reservas.detalleu as usado,
						reservas.interno as interno,
						reservas.internou as internou,
						reservas.anulada as anulada
						FROM
						reservas
						Inner Join clientes ON reservas.idcliente = clientes.idcliente
						WHERE
						clientes.nombre <> ''
						AND
						anulada = 1
						AND
						idusuario = ". $ase_suc["idusuario"]." ORDER BY idgrupo";
						$ases_sucs_vtas=mysqli_query($con, $SQL);
						// $ase_suc_vta=mysqli_fetch_array($ases_sucs_vtas);
						?>

						<table>

							<!-- <thead>
								<tr>
									<td width="7%"></td>
									<td width="7%"></td>
									<td width="7%"></td>
									<td width="30%"></td>
									<td width="30%"></td>
								</tr>
							</thead> -->


						<?php
						$nro_ase = 1;
						while ($vtas=mysqli_fetch_array($ases_sucs_vtas)) {

						$SQL="SELECT * FROM grupos WHERE idgrupo=".$vtas['grupo'];
						$gru=mysqli_query($con, $SQL);
						if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

						$SQL="SELECT * FROM modelos WHERE idmodelo=".$vtas['modelo'];
						$mod=mysqli_query($con, $SQL);
						if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}


						?>

						<tbody>
								<tr>
									<td width="7%" class="text_center"><?php echo $nro; ?></td>
									<td width="7%" class="text_center"><?php echo $nro_ase; ?></td>
									<td width="7%" class="text_center"> <?php echo $vtas["interno"].$vtas["internou"]; ?></td>
									<td width="7%" class="text_center"> <?php echo $vtas["compra"]; ?></td>
									<td width="30%"><?php echo $grupo['grupo']." ".$modelo['modelo']."".$vtas["usado"]; ?></td>
									<td width="30%"><?php echo $vtas["cliente"] ?></td>
								</tr>
						</tbody>

						<?php
						$nro = $nro + 1;
						$nro_ase = $nro_ase +1;
						 }?>

						</table>

						<?php } ?>


				</div>

			</div>

			<hr>

			<?php } ?>

		<div class="det_vtas" id="det_vtas">


		</div>

	</div>


	<div class="canceladas">
	</div>
</body>
</html>