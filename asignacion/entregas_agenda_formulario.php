<?php 
	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);
	@session_start();
	$id_usuario = $_SESSION["id"];
	$id_perfil = $_SESSION["idperfil"];

	$unidad['interno']='';
	$unidad['cliente']='';
	$unidad['grupo']='';
	$unidad['modelo']='';
	$unidad['asesor']='';
	$lectura = "";
	
if ($nro_unidad!='') {
	$lectura = "readonly='readonly'";
	$SQL="SELECT * FROM view_asignaciones WHERE nro_unidad = ".$nro_unidad;
	$res=mysqli_query($con, $SQL);
	$cant_res = mysqli_num_rows($res);
	$unidad=mysqli_fetch_array($res);
}

?>

<div class="carga-masiva" >
	<form class="form-turno cuadro" action="" method="POST" style="padding:10px;">
		<input type="hidden" id="id_turno" name="id_turno" value="<?php echo $id_turno ?>">
		<input type="hidden" id="nro_unidad" name="nro_unidad" value="<?php echo $nro_unidad ?>">
		<input type="hidden" id="id_sucursal" name="id_sucursal" value="<?php echo $id_sucursal ?>">
		<input type="hidden" id="id" name="id" value="<?php echo $id ?>">

		<div class="titulo centrar-texto">
			RESERVA DE TURNO PARA ENTREGAS
		</div>

		<div class="form-linea">
			<div class="form-linea ancho-40">
				<label class="ancho-35" for="">Fecha</label>
				<input class="form-inputs negrita" type="date"  id="fecha" name="fecha" value="<?php echo $fecha; ?>" readonly="readonly">
			</div>
			<div class="form-linea ancho-40 derecha-texto">
				<label class="ancho-30" for="">Horario</label>
				<div class="ancho-70 centrar-texto">
					<input class="form-inputs negrita" type="text" size="12" id="horario" name="horario" value="<?php echo $horario ?>" readonly="readonly">
				</div>
			</div>
		</div>
		<div class="form-linea">
			<hr class="ancho-100">
		</div>
		<div class="form-linea">
			<div class="form-linea ancho-35">
				<label class="ancho-30" for="">Interno</label>
				<input class="form-inputs" type="text" size="7" id="interno" name="interno" placeholder="Interno" value="<?php echo $unidad['interno'] ?>" autocomplete="off" required <?php echo $lectura; ?>>	
				<a href="" id="click-buscar-interno"><span class="icon-search""></span></a> 
			</div>
			<div class="form-linea ancho-65 derecha-texto">
				<label class="ancho-20" for="">Vehículo</label>
				<div class="ancho-80" id='zona_cliente_uno'>
					<input class="form-inputs" type="text" size="30" id="vehiculo" name="vehiculo" value="<?php echo $unidad['grupo']. ' '.$unidad['modelo']; ?>" readonly="readonly">
				</div>
			</div>
		</div>
		<div class="form-linea">
			<div class="form-linea ancho-55">
				<label class="ancho-25" for="">Cliente</label>
				<input class="form-inputs ancho-85" type="text" id="cliente" name="cliente" value="<?php echo $unidad['cliente']; ?>" readonly="readonly">
			</div>
			<div class="form-linea ancho-40 derecha-texto">
				<label class="ancho-30" for="">Asesor</label>
				<div class="ancho-60">
					<input class="form-inputs ancho-100" type="text" id="asesor" name="asesor" value="<?php echo $unidad['asesor'] ?>" readonly="readonly">
				</div>
			</div>
		</div>
		<div class="form-linea">
			<hr class="ancho-100">
		</div>
		<div class="form-linea">
			<div class="form-linea ancho-40">
				<label class="ancho-80" for="">Unidad Cancelada</label>
				<input class="form-inputs" type="checkbox"  id="unidad_cancelada" name="unidad_cancelada" value="<?php echo $fecha; ?>" readonly="readonly" disabled>
			</div>
			<div class="form-linea ancho-40 derecha-texto">
<!-- 				<label class="ancho-30" for="">Horario</label>
				<div class="ancho-70 centrar-texto">
					<input class="form-inputs" type="text" size="12" id="" name="" value="<?php echo $horario ?>" readonly="readonly">
				</div> -->
			</div>
		</div>
		<div class="zona-botones">
			<div class="form-linea">
			<?php
			if ($nro_unidad!='') {
				if (($unidad['id_asesor']==$id_usuario) OR $id_perfil!= 3 ) {?>
				<div class="ancho-30">
					<input type="submit" class="botones btn-levantar" value="Levantar">
				</div>
			<?php }
			} ?>
		
				<div class="ancho-30">
					<input type="submit" class="botones btn-cancelar" value="Cancelar">
				</div>
				<div class="ancho-30 derecha-texto">
					<?php if ($id_perfil!= 3 OR $id_sucursal != 1): ?>
					<input type="submit" class="botones btn-aceptar" value="Aceptar">						
					<?php endif ?>
				</div>
			</div>
		</div>



	</form>
</div>

<script src="js/entregas_turnos_formulario.js"></script>