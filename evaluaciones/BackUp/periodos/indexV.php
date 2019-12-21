



<div class="zona_tabla ancho-100">

	<table>
		<colgroup>
			<col width="20%">
			<col width="20%">
			<col width="20%">
			<col width="20%">
			<col width="20%">
		</colgroup>
		<thead>
			<tr>
				<td>Titulo 1</td>
				<td>Titulo 2</td>
				<td>Titulo 3</td>
				<td class="celda-sin-borde">Titulo 4</td>
				<td>Titulo 5</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Contenido col 1</td>
				<td>Contenido col 1</td>
				<td>Contenido col 1</td>
				<td class="celda-sin-borde">Contenido col 1</td>
				<td>Contenido col 1</td>
			</tr>
			<tr>
				<td>Contenido col 2</td>
				<td>Contenido col 2</td>
				<td>Contenido col 2</td>
				<td class="celda-sin-borde">Contenido col 2</td>
				<td>Contenido col 2</td>
			</tr>
			<tr>
				<td>Contenido col 3</td>
				<td>Contenido col 3</td>
				<td>Contenido col 3</td>
				<td class="celda-sin-borde">Contenido col 3</td>
				<td>Contenido col 3</td>
			</tr>
			<tr class="fila-sin-borde">
				<td>Contenido col 4</td>
				<td>Contenido col 4</td>
				<td>Contenido col 4</td>
				<td class="celda-sin-borde">Contenido col 4</td>
				<td>Contenido col 4</td>
			</tr>
			<tr>
				<td>Contenido col 5</td>
				<td>Contenido col 5</td>
				<td>Contenido col 5</td>
				<td class="celda-sin-borde">Contenido col 5</td>
				<td>Contenido col 5</td>
			</tr>	
		</tbody>
	</table>
</div>

<div id="zona_formulario">
	<form>

		<div class="form-renglon">

			<div class="form-columna ancho-50">
				<label class="form-label" for="campo_uno">Nombre Label</label>
				<input class="form-input" type="text" name="campo_uno" value="" placeholder="">
			</div>

			<div class="form-columna ancho-50">
				<label class="form-label" for="campo_dos">Nombre Label</label>
				<input  class="form-input" type="text" name="campo_dos" value="" placeholder="">
			</div>
		</div>

		<div class="form-renglon">

			<div class="form-columna ancho-50">

				<label class="form-label" for="select_uno">Nombre Label</label>
				<select class="form-select" name="select_uno" id="">
					<option value="0" selected>Seleccione Opcion</option>
					<option value="">Opcion 1</option>
					<option value="">Opcion 2</option>
					<option value="">Opcion 3</option>
				</select>

			</div>
			<div class="form-columna ancho-50">

				<label class="form-label" for="select_dos">Nombre Label</label>
				<select class="form-select" name="select_dos" id="">
					<option value="0" selected>Seleccione Opcion</option>
					<option value="">Opcion 1</option>
					<option value="">Opcion 2</option>
					<option value="">Opcion 3</option>
				</select>

			</div>
		</div>

		<div class="form-renglon">
			<div class="form-columna ancho-50">
				<label for="gender" class="form-label">Seleccionar radio bottom</label>
					  <input class="form-radio" type="radio" name="gender" value="male"> Male
						<!-- 			  <input class="form-radio" type="radio" name="gender" value="female"> Female
									  <input class="form-radio" type="radio" name="gender" value="other"> Other -->
			</div>
		</div>

		<div class="form-renglon">
			<div class="form-columna ancho-50">
				<input class="form-btn cancelar" type="submit" value="Cancelar">
				<button type="submit" class="form-btn cancelar">Enviar </button>
			</div>
			<div class="form-columna ancho-50 flexible flexible-derecha">
				<input class="form-btn aceptar" type="submit" value="Aceptar">
			</div>
		</div>

	</form>
	<!-- *************************************************************** -->
</div>

