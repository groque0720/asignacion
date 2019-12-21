
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	// $SQL="SELECT * FROM usuarios WHERE perfil=3 AND activo=1";
	// $asesores = mysqli_query($con, $SQL);

 ?>

<div class="titulo-modelo flexible justificar menu-pmi">
	<div>PLAN DE MENSUAL INDIVIDUAL</div>
	<div><a id="imprimir" href="">Imprimir</a></div>
</div>

<div class="titulo-modelo margen-abajo-10 centrar-caja flexible justificar menu-pmi" style="margin-bottom: 10px;">
	<div class="periodo">
		<label for="">Mes</label>
		<?php 
			$SQL="SELECT * FROM meses";
			$meses = mysqli_query($con, $SQL);
		 ?>
		<select name="mes" id="mes" class="definir">
			<option value="0"></option>
			<?php 
				while ($mes=mysqli_fetch_array($meses)) { ?>
					<option value="<?php echo $mes['idmes'] ?>"><?php echo $mes['mes']; ?></option>
			<?php } ?>

		</select>

		<label for="">Año</label>
		<input type="text" value="<?php echo date('Y'); ?>" placeholder="Año" size="5" name="ano" id="ano" class="definir">

	</div>
	<div class="asesor">

		<label for="">Asesor</label>

		<?php

			$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
			$usuarios=mysqli_query($con, $SQL);
			$usuario_a[1]['nombre']= '-';
			$i=1;
			while ($usuario=mysqli_fetch_array($usuarios)) {
				$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
				$i++;
			}

			//$SQL="SELECT * FROM ect_asesores WHERE borrar=0 ORDER BY posicion";
			$SQL="SELECT * FROM ect_view_asesores_activos ORDER BY asesor";
			$asesores=mysqli_query($con, $SQL);
		 ?>
		 <select name="asesor" id="asesor" class="definir">
		 	<option value="0"></option>
		 	<?php while ($asesor = mysqli_fetch_array($asesores)) { ?>
		 		
				<option value="<?php echo $asesor['id_asesor_ect']; ?>"><?php echo $usuario_a[$asesor['id_usuario']]['nombre']; ?></option>
		 	<?php } ?>
		 </select>
		
	</div>
</div>
<div id="zona_definicion_pmi">
	<?php //include('plan_mensual_individual_cuerpo.php'); ?>	
</div>

<script src="js/plan_mensual_individual.js"></script>

