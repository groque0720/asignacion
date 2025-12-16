<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f7fa;
	font-size: 11px;
}

.header-page {
	margin: 20px auto;
	width: 80%;
	display: flex;
	justify-content: space-between;
	margin-bottom: 20px;
	font-size: 14px;
	color: #2c3e50;	
}

table {
    border-collapse: collapse;
    width: 80%;
    background: #fff;
    font-size: 12px;
	margin: auto;
	box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

thead {
	position: sticky;
	top: 0;
	z-index: 1;
}

th {
    background: #2c3e50;
    color: #fff;
    padding: 6px;
    /* position: sticky; */
    /* top: 0; */
    z-index: 2;
}

td {
    padding: 6px 8px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

td.left {
    text-align: left;
}

tr.group-header td {
    background: #dfe6e9;
    font-weight: bold;
    text-align: left;
    font-size: 14px;
}

tr.group-total td {
    background: #ecf0f1;
    font-weight: bold;
    border-top: 2px solid #999;
}

tr.grand-total td {
    background: #34495e;
    color: #fff;
    font-weight: bold;
    border-top: 3px solid #000;
}

tr:hover td {
    background: #f1f8ff;
}


.stock {
	background-color: #f1f5faff;
}
.stock-second {
	color: #999;
}

.llegadas {
	background-color: #f2faf2ff;	
}

.llegadas {
	background-color: #e4f7e4ff;	
}
.no_llegadas {
	background-color: #fcf4e7ff;	
}
</style>



<?php
	include("../funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
 ?>

<?php
	$SQL = "SELECT
				g.idgrupo,
				g.grupo,
				m.idmodelo,
				m.modelo,

				/* ===== TOTALES ===== */
				COUNT(DISTINCT a.id_unidad) AS stock_total,

				/* ===== ESTADO COMERCIAL ===== */
				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.estado_reserva = 1 
					THEN 1 ELSE 0 
				END) AS con_cliente,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.reventa = 1 
					THEN 1 ELSE 0 
				END) AS reventas,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.estado_reserva = 0 
					AND a.id_asesor = 2 
					THEN 1 ELSE 0 
				END) AS EFV,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.estado_reserva = 0 
					AND a.id_asesor != 2 
					THEN 1 ELSE 0 
				END) AS libres,

				/* ===== ARRIBOS ===== */
				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NOT NULL 
					THEN 1 ELSE 0 
				END) AS llegadas,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NOT NULL 
					AND a.estado_reserva = 0
					AND a.id_asesor != 2 
					THEN 1 ELSE 0 
				END) AS llegadas_sin_cliente,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NOT NULL 
					AND a.estado_reserva = 1 
					AND a.id_asesor != 2 
					THEN 1 ELSE 0 
				END) AS llegadas_con_cliente,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NOT NULL 
					AND a.estado_reserva = 0 
					AND a.id_asesor = 2 
					THEN 1 ELSE 0 
				END) AS llegadas_sin_cliente_EFV,

				/* ===== NO ARRIBOS ===== */
				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NULL 
					THEN 1 ELSE 0 
				END) AS no_llegadas,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NULL 
					AND a.estado_reserva = 0 
					AND a.id_asesor != 2
					THEN 1 ELSE 0 
				END) AS no_llegadas_sin_cliente,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NULL 
					AND a.estado_reserva = 1 
					AND a.id_asesor != 2 
					THEN 1 ELSE 0 
				END) AS no_llegadas_con_cliente,

				SUM(CASE 
					WHEN a.id_unidad IS NOT NULL 
					AND a.fec_arribo IS NULL 
					AND a.estado_reserva = 0 
					AND a.id_asesor = 2 
					THEN 1 ELSE 0 
				END) AS no_llegadas_sin_cliente_EFV

			FROM grupos AS g
			INNER JOIN modelos AS m 
				ON g.idgrupo = m.idgrupo  

			LEFT JOIN asignaciones AS a 
				ON m.idmodelo = a.id_modelo
				AND a.borrar = 0
				AND a.entregada = 0
				AND a.estado_tasa = 1
				AND a.id_negocio = 1

			WHERE g.activo = 1 
			AND m.activo = 1
			AND g.idgrupo != 14

			GROUP BY g.idgrupo, m.idmodelo
			ORDER BY g.posicion, m.posicion
			";


$result=mysqli_query($con, $SQL);

function initTotales() {
    return [
		'llegadas'=>0,'llegadas_sin_cliente'=>0,'llegadas_sin_cliente_EFV'=>0,'llegadas_con_cliente'=>0,
        'no_llegadas'=>0,'no_llegadas_sin_cliente'=>0,'no_llegadas_sin_cliente_EFV'=>0,'no_llegadas_con_cliente'=>0,
        'stock_total'=>0,'libres'=>0,'con_cliente'=>0,'reventas'=>0,'EFV'=>0
    ];
}

function v($value) {
    return ($value == 0 || $value === null) ? '-' : $value;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Estado de Stock - TASA</title>
	<link rel="shortcut icon" type="image/x-icon" href="../imagenes/favicon.ico" />
</head>
<body>


<div class="header-page">
	<div style="display: flex; align-items: center;">
		<img src="../imagenes/favicon.ico" alt="">
		<strong>DyV - CONFIRMADA TASA</strong>
	</div>
	<div style="display: flex; align-items: center;">
		Estado de Stock
		<!-- <?php echo date("d/m/Y H:i:s"); ?> -->
		
		<a href="./estado_stock_pdf.php" target="_blank" style="text-decoration: none; color: #2c3e50; margin-left: 15px;">
			<span style="font-size: 26px; font-weight: bold;">🖨</span>
			<span>imprimir</span>
		</a>
		
	</div>

</div>


<table>
	<thead>
	<tr>
		<th rowspan="2" >Modelo</th>
		<th colspan="4" >Stock Con Arribo</th>
		<th colspan="4" >Stock Sin Arribo</th>
		<th colspan="5" >Stock Total</th>
	</tr>
	<tr>


		<th style="max-width: 80px;" >Total</th>
		<th style="max-width: 80px;" >s/cli</th>
		<th style="max-width: 80px;" >c/cli</th>
		<th style="max-width: 80px;" >EFV</th>

		<th style="max-width: 80px;" >Total</th>
		<th style="max-width: 80px;" >s/cli</th>
		<th style="max-width: 80px;" >c/cli</th>
		<th style="max-width: 80px;" >EFV</th>

		<th style="max-width: 80px;">Total</th>
		<th style="max-width: 80px;">s/cli</th>
		<th style="max-width: 80px;" >c/cli</th>
		<th style="max-width: 80px;"class="stock-second">Rev</th>
		<th style="max-width: 80px;">EFV</th>

	</tr>
	</thead>

<?php

	$grupo_actual = null;
	$totales_grupo = [];
	$totales_generales = [];


	while ($row = mysqli_fetch_assoc($result)) {

		if ($grupo_actual !== $row['grupo']) {

			// cerrar grupo anterior
			if ($grupo_actual !== null) {
				echo "<tr class='group-total'><td>Subtotal $grupo_actual</td>";
				foreach ($totales_grupo as $v) echo "<td>$v</td>";
				echo "</tr>";
			}

			// nuevo grupo
			echo "<tr class='group-header'><td colspan='14'>{$row['grupo']}</td></tr>";
			$grupo_actual = $row['grupo'];
			$totales_grupo = initTotales();
		}

		// sumar totales
		foreach ($totales_grupo as $k => $v) {
			$totales_grupo[$k] += $row[$k];
			$totales_generales[$k] = ($totales_generales[$k] ?? 0) + $row[$k];
		}

		// fila modelo
		echo "
		<tr>


			<td class='left'>{$row['modelo']}</td>
			<td class='llegadas' style='border-left: 2px solid #000;'>".v($row['llegadas'])."</td>
			<td class='llegadas' style='border-left: 1px solid #000;'>".v($row['llegadas_sin_cliente'])."</td>
			<td class='llegadas'>".v($row['llegadas_con_cliente'])."</td>
			<td class='llegadas'>".v($row['llegadas_sin_cliente_EFV'])."</td>

			<td class='no_llegadas' style='border-left: 2px solid #000;'>".v($row['no_llegadas'])."</td>
			<td class='no_llegadas' style='border-left: 1px solid #000;'>".v($row['no_llegadas_sin_cliente'])."</td>
			<td class='no_llegadas'>".v($row['no_llegadas_con_cliente'])."</td>
			<td class='no_llegadas'>".v($row['no_llegadas_sin_cliente_EFV'])."</td>

			<td class='stock' style='border-left: 2px solid #000;'>".v($row['stock_total'])."</td>
			<td class='stock' style='border-left: 2px solid #000;'>".v($row['libres'])."</td>
			<td class='stock'>".v($row['con_cliente'])."</td>
			<td class='stock stock-second'>".v($row['reventas'])."</td>
			<td class='stock'>".v($row['EFV'])."</td>

		</tr>";
	}

	// último grupo
	echo "<tr class='group-total'><td>Subtotal $grupo_actual</td>";
	foreach ($totales_grupo as $v) echo "<td>$v</td>";
	echo "</tr>";
	
	echo "<tr class='group-total'><td>Subtotal $grupo_actual</td>";
	foreach ($totales_grupo as $v) echo "<td>$v</td>";
	echo "</tr>";

	// total general
	echo "<tr class='grand-total'><td>TOTAL GENERAL</td>";
	foreach ($totales_generales as $v) echo "<td>$v</td>";
	echo "</tr>";

?>

</table>

</body>
</html>