<!DOCTYPE html>
<html lang="es">
<head>
    <title>Detalle de Creditos</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
     <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_sol_p.css">
     <link rel="stylesheet" href="../css/jquery-ui.css" />
  	<script src="../js/jquery-1.9.1.js"></script>
  	<script src="../js/jquery-ui.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

		$(".boton").click(function(event) {
		document.location.href ="detalle_ventas_resumen.php";
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

				<div id="alta_sol" style="width: 40%; float: left;">
					<!-- <span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Resumen de Cr&eacute;ditos</span> -->
				</div>
				<div id="alta_sol" style="width: 10%; float: right;">
					<input type="button" class="boton" value="Resumen" style="background:#7093DB; color:#fff; padding: 5px; border-radius: 5px;"/>
				</div>

			</div>
			<div id="cuerpo_asesor">

			<?php

			include("../funciones/func_mysql.php");
			conectar();
			//mysql_query("SET NAMES 'utf8'");
			$SQL="SELECT * FROM financieras ORDER BY financiera";
			$financieras=mysqli_query($con, $SQL);

			while ($financiera=mysqli_fetch_array($financieras)) { ?>
			<span style="font-size:1.3em; color:red;"><?php  echo $financiera['financiera'];?></span><hr>

			<?php



				$SQL="SELECT * FROM tipos_creditos ORDER BY tipocredito";
				$creditos=mysqli_query($con, $SQL);

				while ($credito = mysqli_fetch_array($creditos)) {



					$SQL="SELECT
					reservas.idreserva AS idreserva,
					reservas.fecres AS fecres,
					reservas.compra AS compra,
					clientes.nombre AS cliente,
					usuarios.nombre AS asesor,
					tipos_creditos.tipocredito AS credito,
					financieras.financiera AS financiera,
					lineas_detalle.monto AS monto,
					reservas.detalleu AS detalleu,
					reservas.idgrupo,
					reservas.idmodelo,
					reservas.idcredito AS idcredito,
					creditos.estado AS estado
					FROM
					reservas
					Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
					Inner Join codigos ON lineas_detalle.idcodigo = codigos.idcodigo
					Inner Join tipos_creditos ON codigos.tipocredito = tipos_creditos.idtipocredito
					Inner Join financieras ON codigos.financiera = financieras.idfinanciera
					Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
					Inner Join clientes ON reservas.idcliente = clientes.idcliente
					Inner Join creditos ON reservas.idcredito = creditos.idcredito
					WHERE
					codigos.credito =  '1' AND
					reservas.anulada =  '0' AND
					financieras.financiera = '".$financiera['financiera']."' AND
					tipos_creditos.tipocredito = '".$credito['tipocredito']."'"  ;

					$resultados=mysqli_query($con, $SQL);
					$band=false;
					$numero=0;
					$numero = mysql_num_rows($resultados);

					if ($numero>0) {$band=true;?>
						<span style="font-size:1.1em; color:blue; margin-left: 40px;"><?php  echo $credito['tipocredito'];?></span><br>

						<table rules="all" border="1" style="width:50%; text-align:center; margin:0 auto;">
							<thead>
								<td></td>
								<td>Pendientes</td>
								<td>Aprobados</td>
								<td>Total</td>
							</thead>
					<?php } ?>


					<?php
					$nuevo_p=0;
					$nuevo_a=0;
					$usado_p=0;
					$usado_a=0;
					while ($res=mysqli_fetch_array($resultados)) {

						if ($res['compra']=="Nuevo" && ($res['estado']==6 || $res['estado']==7))  {
							$nuevo_a=$nuevo_a + 1;
						}
						if ($res['compra']=="Nuevo" && $res['estado']!=7 && $res['estado']!=6) {
							$nuevo_p=$nuevo_p + 1;
						}
						if ($res['compra']=="Usado" && ($res['estado']==6 || $res['estado']==7)) {
							$usado_a=$usado_a + 1;
						}
						if ($res['compra']=="Usado" && $res['estado']!=7 && $res['estado']!=6) {
							$usado_p=$usado_p + 1;
						}
					 } ?>

					 <?php if ($band==true) {?>

					<tbody>
						<tr>
							<td>Nuevo</td>
							<td><?php echo $nuevo_p ?></td>
							<td><?php echo $nuevo_a ?></td>
							<td><?php echo $nuevo_p + $nuevo_a ?></td>
						</tr>
						<tr>
							<td>Usado</td>
							<td><?php echo $usado_p ?></td>
							<td><?php echo $usado_a ?></td>
							<td><?php echo $usado_p + $usado_a ?></td>
						</tr>
						<tr style="background: #ccc;">
							<td>Total</td>
							<td><?php echo $nuevo_p + $usado_p ?></td>
							<td><?php echo $nuevo_a + $usado_a ?></td>
							<td><?php echo $nuevo_p + $usado_p + $nuevo_a + $usado_a ?></td>
						</tr>

					</tbody>
					<?php } ?>
					</table>


				<?php } //final credito?>

			<?php } //final financieras?>
			</div>
		</section>

	</div>

</body>
<?php  mysqli_close($con);  ?>
</html>
