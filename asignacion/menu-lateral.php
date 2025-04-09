<?php
ini_set('display_errors', 'On');
ini_set('display_errors', 1);

$SQL = "SELECT * FROM grupos WHERE grupo <> '' AND activo = 1 ORDER BY posicion";
$grupos = mysqli_query($con, $SQL);
$cont_modelo = 1;
?>
<!-- Stylesheets -->
<link rel="stylesheet" href="css/estilo-menu-lateral.css">
<div class="menu-lateral">
	<ul id="accordion" class="accordion">
		<?php
		while ($grupo = mysqli_fetch_array($grupos)) { ?>
			<li>
				<?php
				$id_usado = '';
				if ($grupo['grupo'] == 'USADOS') {


					// BLOQUE SOLO VISTO POR USARIOS ESPECIFICOS (Mauro vargas, Nicolas Burgos(89), Fredy Vargas (41) )

					// if($_SESSION["id"] == 56 || $_SESSION["id"] == 103 || $_SESSION["id"] == 41 || $_SESSION["id"] == 89 ) {
					// 	$id_usado='link_usado';
					// }else{
					// 	$id_usado='bloqueado';
					// }

					// HABILITADOS PARA TODOS

					$id_usado = 'link_usado';
				}
				?>
				<?php  ?>
				<div class="link" <?php echo "id='" . $id_usado . "'"; ?> data-grupo="<?php echo $grupo['idgrupo']; ?>">
					<i class="icon-user fa"></i>
					<?php echo $grupo['grupo']; ?>

					<?php if ($id_usado == 'bloqueado') {  ?>
						🔐 ⚙️ <span style="color:red">Bloqueado</span>
					<?php } ?>

					<i class="fa icon-chevron-down"></i>
				</div>
				<ul class="submenu">

					<?php

					$SQL = "SELECT * FROM modelos WHERE activo = 1 AND idgrupo = " . $grupo['idgrupo'] . " ORDER BY posicion";
					$modelos = mysqli_query($con, $SQL);
					while ($modelo = mysqli_fetch_array($modelos)) {
						if ($cont_modelo == 1) {
							$cont_modelo++;
							$modelo_activo = $modelo['idmodelo'];
							$grupo_activo = $grupo['idgrupo'];
						}
					?>
						<li><a class="item-menu-lateral" href="#" data-id="<?php echo $modelo['idmodelo']; ?>"><?php echo $modelo['modelo']; ?></a></li>
					<?php } ?>


				</ul>
			</li>
		<?php } ?>
		<!-- Contenedor -->
		<input type="hidden" id="modelo_activo" value="<?php echo $modelo_activo; ?>">
		<input type="hidden" id="grupo_activo" value="<?php echo $grupo_activo; ?>">
		<?php
		$SQL = "SELECT MAX(id_act) as id_act FROM a_modificaciones WHERE modelo_activo = $modelo_activo";
		$modificaciones = mysqli_query($con, $SQL);
		$result = mysqli_fetch_array($modificaciones);
		?>
		<!-- busco el mayor numero para que no me actualice apenas entro. -->
		<input type="hidden" id="nro_act" value="<?php echo $result['id_act']; ?>">

	</ul>
</div>
<!-- Scripts -->
<script src="js/menu-lateral.js"></script>