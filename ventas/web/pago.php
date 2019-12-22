<!DOCTYPE html>
<html lang="es">
<head>
    <title>Area Pagos</title>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">

 <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
 <script src="../js/jquery-1.9.1.js"></script>
 <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
 <script type="text/javascript" src="../js/abm_pagos.js"></script>

<style>

@media print {
	#imagen {
		float: right;
	}
	#imagen img {
		width: 200px;
	}


}
</style>

</head>

<body>
<div id="agrupar">

		<?php include("../includes/header.php") ?>

		<?php

		include("../funciones/func_mysql.php");
		conectar();
		//mysql_query("SET NAMES 'utf8'");
		$totalop=0;



		$idrecord=$_GET["IDrecord"]; // id cliente

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
				reservas.idcredito  AS idcredito
				FROM
				reservas
				Inner Join lineas_detalle ON reservas.idreserva = lineas_detalle.idreserva
				Inner Join codigos ON lineas_detalle.idcodigo = codigos.idcodigo
				Inner Join tipos_creditos ON codigos.tipocredito = tipos_creditos.idtipocredito
				Inner Join financieras ON codigos.financiera = financieras.idfinanciera
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				WHERE
				codigos.credito =  '1' AND
				reservas.cancelada =  '0' AND reservas.idcliente=".$_GET["IDrecord"];
				$res=mysqli_query($con, $SQL);
				$credito=mysqli_fetch_array($res);



				$SQL=" SELECT reservas.idreserva AS idreserva,
								clientes.nombre AS cliente,
								usuarios.nombre AS asesor,
								reservas.idgrupo,
								reservas.idmodelo,
								reservas.idcredito  AS idcredito
				FROM
				reservas
				Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
				Inner Join clientes ON reservas.idcliente = clientes.idcliente
				WHERE
				reservas.idcliente=".$_GET["IDrecord"];
				$res=mysqli_query($con, $SQL);
				$cliente=mysqli_fetch_array($res);

				$nrores=$cliente["idreserva"];


			 ?>

		<div id="alta_sol">

			<a href="javascript:window.history.back();">&laquo; Volver atrás</a>

		</div>
		<section id="seccion">

			<div class="fila">
				<div  style="width: 100%; float: left; text-align:center">
					<span style="font-size: 1.3em; color: blue; font-weight: bold; text-transform: uppercase; font-style: italic;">Detalle de Estado de Cuenta</span>
				</div>
				<input type="hidden" id="idcredito" name="idcredito" value="<?php echo $credito["idcredito"]; ?>">
				<input type="hidden" id="nroreserva" name="nroreserva" value="<?php echo $cliente["idreserva"]; ?>">
				<input type="hidden" id="movimiento" name="movimiento" value="">
				<input type="hidden" id="nroli" name="nroli" value="">

			</div>
			<hr>

			<div id="cuerpo_asesor">

			<div>
				<div class="fila">
						<input id="idusu" name="idusu" type="hidden" value="<?php echo $_SESSION["id"]; ?>">
						<input id="idperfil" name="idperfil" type="hidden" value="<?php echo $_SESSION["idperfil"]; ?>">
					<div  style="width: 50%; float: left;">
						<label>Cliente:</label>
						<input style="text" id="cliente" name="cliente" value="<?php echo $cliente["cliente"]; ?>" size="40">
					</div>

					<div  style="width: 50%; float: right; text-align:center;">
						<label>Asesor:</label>
						<input style="text" id="asesor" name="asesor" value="<?php echo $cliente["asesor"]; ?>" size="40">
					</div>
				</div>
				<hr>
				<div class="fila">

					<div  style="width: 35%; float: left;">
						<label>Tipo Cr&eacute;dito:</label>
						<input style="text" id="credito" name="credito" value="<?php echo $credito["credito"]; ?>" size="25">
					</div>
					<div  style="width: 35%; float: left; text-align:center;">
						<label>Financiera:</label>
						<input style="text" id="financiera_a" name="financiera_a" value="<?php echo $credito["financiera"]; ?>" size="25">
					</div>
					<div  style="width: 25%; float: right;">
						<label>Monto:</label>
						<input style="text" id="montofinanciera" name="montofinanciera" value="<?php echo number_format($credito['monto'], 2, ',','.'); ?>" size="15">
					</div>

				</div>
				<hr>
				<div class="fila" style="background:#ccc;">

					<div  style="width: 40%; float: left;">

						<?php
							$SQL="SELECT sum(monto) as total FROM lineas_detalle WHERE movimiento = 1 and idreserva =".$cliente["idreserva"];
							$result=mysqli_query($con, $SQL);
							if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;
							?>

						<label>Monto Operación:</label>
						<input style="text" id="montooperacion" name="montooperacion" value="<?php $s1=$total_op["total"]; echo number_format($total_op["total"], 2, ',','.'); ?>" size="25">
					</div>
					<div  style="width: 30%; float: left; text-align:center;">

						<?php
							$SQL="SELECT sum(monto) as total FROM pagos_lineas WHERE idreserva =".$cliente["idreserva"];
							$result=mysqli_query($con, $SQL);
							if (empty($result)){$total_op["total"]=0;}else{$total_op=mysqli_fetch_array($result);} ;
							?>

						<label>Pagado:</label>
						<input style="text" id="pagado" name="pagado" value="<?php $s2=$total_op["total"]; echo number_format($total_op["total"], 2, ',','.'); ?>" size="25">
					</div>
					<div  style="width: 25%; float: right;">
						<label>A cancelar:</label>
						<input style="text" id="acancelar" name="acancelar" value="<?php echo number_format(($s1-$s2), 2, ',','.'); ?>" size="15">
					</div>

				</div>

				<hr>



				<div  id="cargaestado" class="fila" style="margin-bottom: 10px; font-size:1em;">

					<div id="cargar_det" style="width:90%; display:block; margin-left:10px;">

						<input type="button" id="boton" value="Registrar Pago">


						<div id="dialogo" title="Registrar Pago">

							<div class="fila">
								<label for="fecha">Fecha:</label>
								<div id="act_fec">
								<input type="date" id="fecha" name="fecha" value="">
								</div>
							</div>

							<hr>

							<div class="fila">

								<label for="tipo_pago">Tipo de Pago</label>

								<?php
								$SQL="SELECT * FROM pagos_tipos";
								$res=mysqli_query($con, $SQL);
								 ?>
								<select id="tipo_pago" name="tipo_pago">
									<option value="0" style="color:#ccc; background: red"></option>
									<?php while ($tipo=mysqli_fetch_array($res)) {?>
									<option value="<?php echo $tipo["idtipopago"] ?>"><?php echo $tipo["tipopago"] ?></option>
									<?php } ?>
								</select>

								<label for="tipo_pago">Modo de Pago</label>

								<?php
								$SQL="SELECT * FROM pagos_modos";
								$res=mysqli_query($con, $SQL);
								 ?>
								<select id="modo_pago" name="modo_pago" >
									<option value="0" style="color:#ccc; background: red"></option>
									<?php while ($modo=mysqli_fetch_array($res)) {?>
									<option value="<?php echo $modo["idpagomodo"] ?>"><?php echo $modo["modo"] ?></option>
									<?php } ?>
								</select>
							</div>
							<hr>
							<div class="fila">
								<label for="financiera">Financiera:</label>
								<?php
								$SQL="SELECT * FROM financieras";
								$res=mysqli_query($con, $SQL);
								 ?>

								<select id="financiera" name="financiera" >
									<option value="0" style="color:#ccc; background: red"></option>
									<?php while ($fi=mysqli_fetch_array($res)) {?>
									<option value="<?php echo $fi["idfinanciera"]; ?>"><?php echo $fi["financiera"]; ?></option>
									<?php } ?>
								</select>
							</div>
							<hr>
							<div class="fila">

								<label for="monto_pago">Monto:</label>
								<input type="text" id="monto_pago" name="monto_pago" value="0" size="10" style="text-align:right;">
								<label for="nrorecibo">Nro Recibo:</label>
								<input type="text" id="nrorecibo" name="nrorecibo" size="10" >

							</div>
							<hr>
								<textarea name="observacion" id="observacion" cols="30" rows="5" placeholder="observaci&oacute;n" style="width:100%;"></textarea>
							</div>
						</div>
					</div>
				</div>

				<hr>


					<div id="act_ajax">
						<?php include("pago_cuerpo.php"); ?>
					</div>


			</div>
		</section>





	</div>

</body>
<?php mysqli_close($con); ?>
</html>