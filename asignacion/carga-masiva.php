<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
?>


<div class="carga-masiva">
	<form class="form-unidad" action="" method="POST">
		<div class="titulo centrar-texto">
			CARGA MASIVA DE UNIDADES
		</div>

		<div class="lado inputs-masivo">

			<div class="form-linea ancho-35">
				<label class="ancho-45" for="">Cantidad</label>
				<input class="form-inputs" type="text" size="5" name="cantidad" value="" autocomplete="off" required>
			</div>

			<div class="form-linea ancho-50">
				<label class="ancho-80" for="">Confirmada por Toyota</label>
				<input class="form-inputs" type="checkbox" name="estado_tasa">
			</div>

		</div>
		<div class="lado inputs-masivo">
			<div class="form-linea ancho-30 ">

				<label class="ancho-1-3" for="">Mes</label>
				<select class="form-inputs ancho-2-3" name="id_mes" id="" required>
					<option value="0"></option>
					<?php
						$SQL="SELECT * FROM meses";
						$meses=mysqli_query($con, $SQL);
						while ($mes=mysqli_fetch_array($meses)) {?>
							<option value="<?php echo $mes['idmes'] ?>"><?php echo $mes['mes']; ?></option>
					 <?php } ?>
				</select>

			</div>

			<div class="form-linea ancho-30">
				<label class="an" for="">Año</label>
				<input class="form-inputs" type="text" size="5" name="año" value="" autocomplete="off" required>
			</div>
		</div>

		<div class="lado inputs-masivo" style="display: flex; justify-content: space-between;">
			<div class="ancho-2-6" style="width: 35%;">
				<label class="ancho-20" for="">Modelo</label>
				<select class="form-inputs ancho-2-3" name="id_grupo" id="grupo" required>
				<option value="0"></option>
				<?php
					$SQL="SELECT * FROM grupos WHERE cerokilometro = 1 AND activo = 1 ORDER BY posicion";
					$grupos=mysqli_query($con, $SQL);
					while ($modelo=mysqli_fetch_array($grupos)) { ?>
						<option value="<?php echo $modelo['idgrupo']; ?>"><?php echo $modelo['grupo']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="ancho-4-6" style="width: 65%;">
				<label class="ancho-20" for="">Versión</label>
				<select class="form-inputs ancho-100" name="id_modelo" id="id_modelo" required>
				<option value="0"></option>
				<?php
					$SQL="SELECT * FROM modelos WHERE idgrupo = ".$unidad['id_grupo']." ORDER BY posicion";
					$versiones=mysqli_query($con, $SQL);
					while ($version=mysqli_fetch_array($versiones)) { ?>
						<option value="<?php echo $version['idmodelo']; ?>"><?php echo $version['modelo']; ?></option>
					<?php }	?>
				</select>
			</div>
		</div>
		<div class="lado inputs-masivo">
			<div class="form-linea ancho-100" style="display: flex; align-items: center; margin-top: 3px;">
				<label class="ancho-30" for="">Cliente</label>
				<div class="ancho-70">
					<input class="form-inputs ancho-100" style="padding: 6px;"  type="text"  autocomplete="off" name="reserva_cliente">
				</div>
			</div>
		</div>
		<div class="lado inputs-masivo">
			<div class="ancho-100 " style="display: flex; align-items: center; justify-content: space-between;">
				<label class="ancho-30" style="width: 50px;" for="">Asesor</label>
				<div class="ancho-70">
					<select class="form-inputs ancho-100" name="id_asesor" id="id_asesor">
							<option value="1"></option>
							<?php
								$SQL="SELECT * FROM usuarios WHERE idperfil = 3 AND activo = 1 ORDER BY nombre";
								$usuarios = mysqli_query($con, $SQL);
								while ($usuario=mysqli_fetch_array($usuarios)) { ?>
									<option value="<?php echo $usuario['idusuario']; ?>"><?php echo $usuario['nombre']; ?></option>
								<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<div class="lado inputs-masivo">
			<div class="form-linea ancho-80">
				<label class="ancho-80" for="">Reservada Gerencia (EFV)</label>
				<input class="form-inputs" type="checkbox" size="5" name="reserva_gerencia">
			</div>
		</div>

		<div class="lado inputs-masivo">
			<div class="form-linea ancho-80">
				<label class="ancho-80" for="">No disponible</label>
				<input class="form-inputs" type="checkbox" size="5" name="no_disponible">
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

<script src="js/carga-masiva.js"></script>