<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title></title>
	<link rel="stylesheet" href="../css/reserva.css" />
</head>
<body>
	<div id="reserva">

		<div class="linea" id="sol_uno">

			<div class="col1" id="nro_res">
				<label>Nro</label>
				<input type="text" name="nrores" id="nrores" value="2368" size="2">
			</div>

			<div class="col2" id="nom_asesor">
				<label>Asesor</label>
				<input type="text" name="nomasesor" id="nomasesor" value="Velazquez L." size="10">
			</div>

			<div class="col3" id="tipo_venta">
				<label>Venta</label>
				<select id="tipoventa" name="tipoventa" required>
					<option value=""></option>
					<option value="Convencional">Convencional</option>
					<option value="Reventa" >Reventa</option>
					<option value="Plan Empleado">Plan Empleado</option>
					<option value="Especial">Especial</option>
					<option value="Plan de Ahorro" >Plan de Ahorro</option>
				</select>
			</div>

			<div class="col3" id="fecha_res">
				<label>Fecha Res. </label>
				<input type="date" name="fechares" id="fechares" value="" size="5">
			</div>	

			<div class="col3" id="fecha_act">
				<label>Ult.Act.</label>
				<input type="date" name="fechaact" id="fechaact" value="" size="5">
			</div>
		</div>
		<div class="linea" id="sol_dos">
			<div id="compra" class="col4">
				<label>Compra:</label>
					<select id="compra" name="compra" required>
						<option value="Nuevo">Nuevo</option>
						<option value="Usado">Usado</option>
					</select>
			</div>
			<div class="col4" id="nro_unidad">
				<label>Nro Unidad:</label>
				<input type="text" name="nrounidad" id="nrounidad" size="3">
			</div>
		</div>	


	</div>



	
</body>
</html>