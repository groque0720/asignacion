<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Control de Pagos - Derka y Vargas</title>
<link rel="stylesheet" href="../css/normalize.css">
<link rel="stylesheet" href="../css/pagos.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){

	$("#carga").hide();

	$("#informe").change(function(){

		if ($("#informe").val()==2) {

			
			$("#carga").show();
			var myDate = new Date();
			var displayDate = (myDate.getDate()) + '-' + (myDate.getMonth()+1) + '-' + myDate.getFullYear();
			info=$("#informe option:selected").text();
			leyenda = "<h1> Informe Segun : <strong style='font-style:italic;'>"+info+"</strong> - Fecha: <strong style='font-style: italic;'>"+displayDate+"</strong> </h1> ";
			$("#titulo").html(leyenda);

			idinfo=$("#informe").val();
			
			$.ajax({url:"estado_unidad_filtro.php",cache:false,type:"POST",data:{id:idinfo},success:function(result){
		      	$("#actualizar").html(result);
		      	$("#carga").hide();

		    	}});
		};

		if ($("#informe").val()==1) {
			window.open('../../asignacion/costos_TASA_pdf.php','_blank');
		}
		if ($("#informe").val()==10) {
			window.open('../../asignacion/costos_TASA_pdf_STPA.php','_blank');
		}

		if ($("#informe").val()==3) {
			window.open('../../asignacion/planilla_asignacion_stock_pdf.php','_blank');
		}


	});
});
</script>

</head>
<body>

	<header>
		
		<?php
		include("../funciones/func_mysql.php");
		conectar();
		mysqli_query($con,"SET NAMES 'utf8'");
		@session_start();
		if ($_SESSION["autentificado"] != "SI") {
			//si no existe, envio a la página de autentificacion
			header("Location: ../login");
			//ademas salgo de este script
			exit();
		}

		//  @session_start();
		// //COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
		// if ($_SESSION["autentificado"] != "SI") {
		// 	//si no existe, envio a la página de autentificacion
		// 	header("Location: ../index.php");
		// 	//ademas salgo de este script
		// 	exit();
		// }
		?>

	<input type="hidden" id="perfil" value="<?php echo $_SESSION["idperfil"]; ?>">
		<div id="titulo">
			<h1>Control de Pagos - Derka y Vargas S. A. - Fecha: <?php echo date('d-m-Y'); ?></h1>
		</div>
		
	</header>
	<nav >
		<ul class="menu">
			<li>
				<label for="">Informe Seg&uacute;n:</label>
				<select name="informe" id="informe">
					<option value="0"></option>
					<option value="1">Costos TASA </option>
					<option value="10">Costos TASA (S/TPA/F02)</option>
					<!-- <option value="2">Recurso Cliente</option> -->
					<option value="3">Stock Asignación</option>
					<!-- <option value="3">Fecha de Arribo</option> -->
					<!-- <option value="4">Operaciones Activas (Por fecha de Arribo)</option> -->
					
				</select>
			</li>
			<li>
				<div id="carga">
					<img src="../imagenes/carga.gif" alt="Cargando">
				</div>
			</li>
			
			
		</ul>
<!-- 		<div class="busqueda">
			<input type="text" id="texto_busqueda" name="texto_busqueda">
			<a href="#" class="buscar" id="buscar">Buscar</a>
		</div> -->
	</nav>

	<section>
		<?php	
				$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida_no_llegadas";
				$suc_no_lLegadas = mysqli_query($con, $SQL);

				if (!$suc_no_lLegadas) {
					die("Error en query (no llegadas): " . mysqli_error($con));
				}

				$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida_llegadas";
				$suc_lLegadas = mysqli_query($con, $SQL);

				if (!$suc_lLegadas) {
					die("Error en query (llegadas): " . mysqli_error($con));
				}

				$SQL="SELECT * FROM view_asignaciones_saldo_pendiente_corregida_en_viaje";
				$suc_en_viaje = mysqli_query($con, $SQL);

				if (!$suc_en_viaje) {
					die("Error en query (viaje): " . mysqli_error($con));
				}

				$total_sucursal=[];

		?>
		<div >
			<h4 style="text-align: center;">
				<span>Recursos Derka y Vargas 📅 <?php echo date('d-m-Y'); ?></span>
			</h4>
		</div>
		<div class="top-manu-table">
			<a href="/asignacion/costos_recursos_completa_resumen.php" target="_blank">Imprimir Resumen 🖨️</a>
		</div>

		<div class="zone-table">
			<div>
				<table>
					<thead>
						<tr>
							<th colspan="2">Pendiente Pago TASA</th>
						</tr>
						<tr>
							<th>Sucursal</th>
							<th>Saldo</th>

						</tr>
					</thead>
					<tbody>
						<?php
						$total_no_llegadas = 0;
						$fila=0;
						while ($suc_no_lLegada=mysqli_fetch_array($suc_no_lLegadas)) {
								$total_no_llegadas += $suc_no_lLegada['Saldo'];	?>
						<tr>
							<td>
								<?php //echo $suc_no_lLegada['Sucursal'] ?>
						
							<a href="/asignacion/costos_recursos_pendiente_pago.php?sucursalId=<?php echo $suc_no_lLegada['IdSucursal']; ?>" target="_blank">
								<?php echo $suc_no_lLegada['Sucursal'].' ' ?> 🖨️
							</a>
							</td>
							<td style="text-align: right; padding-right:20px;">
								<?php echo  number_format($suc_no_lLegada['Saldo'], 0, ',','.') ?></td>
								<?php
									$total_sucursal[$fila]['IdSucursal']=$suc_no_lLegada['IdSucursal'];
									$total_sucursal[$fila]['Saldo']=$suc_no_lLegada['Saldo'];
									$total_sucursal[$fila]['Sucursal']=$suc_no_lLegada['Sucursal'];
									$fila++; ?>
						</tr>	<?php } ?>
						<tr class="total">
							<td>
								<a href="/asignacion/costos_recursos_pendiente_pago.php" target="_blank">
									DYV 🖨️
								</a>	
							</td>
							<td style="text-align: right; padding-right:20px;">
								<?php echo  number_format($total_no_llegadas, 0, ',','.') ?></td>	
						</tr>
					</tbody>
				</table>				
			</div>
			<div>
			<table>
					<thead>
						<tr>
							<th colspan="2">En Viaje</th>
						</tr>
						<tr>
							<th>Sucursal</th>
							<th>Saldo</th>

						</tr>
					</thead>
					<tbody>
						<?php
						$total_llegadas = 0;
						$fila=0;
						while ($fila_en_viaje=mysqli_fetch_array($suc_en_viaje)) {
						$total_llegadas += $fila_en_viaje['Saldo'];	?>
						<tr>
							<td> <?php //echo $fila_en_viaje['Sucursal'] ?>
							<a href="/asignacion/costos_recursos_pendiente_en_viaje.php?sucursalId=<?php echo $fila_en_viaje['IdSucursal']; ?>" target="_blank">
								<?php echo $fila_en_viaje['Sucursal'].' ' ?> 🖨️
							</a>
						</td>
							<td style="text-align: right; padding-right:20px;">  <?php echo  number_format($fila_en_viaje['Saldo'], 0, ',','.') ?></td>
							<?php $total_sucursal[$fila]['Saldo']=$total_sucursal[$fila]['Saldo'] + $fila_en_viaje['Saldo']; $fila++; ?>
						</tr>	<?php } ?>
						<tr class="total">
							<td>
								<a href="/asignacion/costos_recursos_pendiente_con_arribo.php" target="_blank">
									DYV 🖨️
								</a>
							</td>
							<td style="text-align: right; padding-right:20px;">
								<?php echo  number_format($total_llegadas, 0, ',','.') ?></td>	
						</tr>
					</tbody>
				</table>			
			</div>
			<div>
			<table>
					<thead>
						<tr>
							<th colspan="2">Con Arribo</th>
						</tr>
						<tr>
							<th>Sucursal</th>
							<th>Saldo</th>

						</tr>
					</thead>
					<tbody>
						<?php
						$total_llegadas = 0;
						$fila=0;
						while ($suc_lLegada=mysqli_fetch_array($suc_lLegadas)) {
								$total_llegadas += $suc_lLegada['Saldo'];	?>
						<tr>
							<td> <?php //echo $suc_lLegada['Sucursal'] ?>
							<a href="/asignacion/costos_recursos_pendiente_con_arribo.php?sucursalId=<?php echo $suc_lLegada['IdSucursal']; ?>" target="_blank">
								<?php echo $suc_lLegada['Sucursal'].' ' ?> 🖨️
							</a>
						</td>
							<td style="text-align: right; padding-right:20px;">  <?php echo  number_format($suc_lLegada['Saldo'], 0, ',','.') ?></td>
							<?php $total_sucursal[$fila]['Saldo']=$total_sucursal[$fila]['Saldo'] + $suc_lLegada['Saldo']; $fila++; ?>
						</tr>	<?php } ?>
						<tr class="total">
							<td>
								<a href="/asignacion/costos_recursos_pendiente_con_arribo.php" target="_blank">
									DyV 🖨️
								</a>
							</td>
							<td style="text-align: right; padding-right:20px;">
								<?php echo  number_format($total_llegadas, 0, ',','.') ?></td>	
						</tr>
					</tbody>
				</table>			
			</div>
			<div>
				<table>
					<thead>
						<tr>
							<th colspan="2">Todas</th>
						</tr>
						<tr>
							<th>Sucursal</th>
							<th>Saldo</th>

						</tr>
					</thead>
					<tbody>
						<?php
						$total_llegadas = 0;
						$fila=1;
						for ($i=0; $i < count($total_sucursal); $i++) {
							$total_llegadas += $total_sucursal[$i]['Saldo'];
							?>
							<tr>
								<td> 
									<a href="/asignacion/costos_recursos_completa.php?sucursalId=<?php echo $total_sucursal[$i]['IdSucursal']; ?>" target="_blank">
										<?php echo $total_sucursal[$i]['Sucursal'].' ' ?> 🖨️
									</a>	
								</td>
								<td style="text-align: right; padding-right:20px;">
									<?php echo  number_format($total_sucursal[$i]['Saldo'], 0, ',','.') ?></td>
							</tr>
							<?php } ?>
						<tr class="total">
							<td>
								<a href="/asignacion/costos_recursos_completa.php" target="_blank">
									DyV  🖨️
								</a>
							</td>
							<td style="text-align: right; padding-right:20px;">
								<?php echo  number_format($total_llegadas, 0, ',','.') ?></td>	
						</tr>
					</tbody>
				</table>				
			</div>

		</div>
	</section>

	
	
</body>

</html>