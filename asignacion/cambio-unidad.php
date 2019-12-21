<div class="carga-masiva">
	<form class="form-unidad" action="" method="POST">
		<div class="titulo centrar-texto">
			CAMBIO DE UNIDAD
		</div>

		<div class="lado inputs-masivo">
			<div class="form-linea ancho-30">
				<label class="ancho-35" for="">Nro Un.</label>
				<input class="form-inputs" type="text" size="5" id="id_nro_uno" name="id_nro_uno" value="" required>
			</div>
			<div class="form-linea ancho-65">
				<label class="ancho-15" for="">Cliente</label>
				<div class="ancho-80" id='zona_cliente_uno'>
					<input class="form-inputs" type="text" size="30" id="cliente_uno" name="cliente_uno" value="" readonly="readonly">
				</div>
			</div>
		</div>

		<div class="zona-img-cambio ancho-100">
				<img class="img-cambio"	src="imagenes/cambio.png" alt="">
		</div>

		<div class="lado inputs-masivo">
			<div class="form-linea ancho-30">
				<label class="ancho-35" for="">Nro Un.</label>
				<input class="form-inputs" type="text" size="5" id="id_nro_dos" name="id_nro_dos" value="" required>
			</div>
			<div class="form-linea ancho-65">
				<label class="ancho-15" for="">Cliente</label>
				<div class="ancho-80" id='zona_cliente_dos'>
					<input class="form-inputs" type="text" size="30" id="cliente_dos" name="cliente_dos" value="" readonly="readonly">
				</div>
			</div>
		</div>

		<div class="zona-botones">
			<div class="form-linea">
				<div class="ancho-50">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
				<div class="ancho-50 derecha-texto">
					<input type="submit" class="botones btn-aceptar" value="Aceptar">
				</div>
			</div>
		</div>
		

	</form>
</div>

<script src="js/cambio-unidad.js"></script>