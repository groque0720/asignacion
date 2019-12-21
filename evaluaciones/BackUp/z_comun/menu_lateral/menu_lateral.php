<?php 

	$SQL="SELECT * FROM aplicaciones_grupos WHERE activo=1";
	$grupos=mysqli_query($con, $SQL); //Grupos de aplicaciones

 ?>

<div class="menu-lateral menu-lateral-oculto" id="wrapper">

	<div class="scrollbar" id="style-3">

		<ul id="accordion" class="accordion">

			<?php 

				while ($grupo=mysqli_fetch_array($grupos)) {

					$SQL="SELECT * FROM view_aplicaciones_grupos_usuario WHERE id_aplicacion_grupo = ".$grupo['id_aplicacion_grupo']." AND id_usuario =".$_SESSION["iduser"];
					$aplicaciones = mysqli_query($con, $SQL);

					$cantidad = mysqli_num_rows($aplicaciones);

					if ($cantidad>=1) { ?>
							<li>

							<?php //cabecera de grupo de aplicaciones ?>

								<div class="link">
									<i class="material-icons">
										<img src="../z_comun/imagenes/<?php echo $grupo['icono']; ?>" alt="">
									</i>
									<?php echo $grupo['aplicacion_grupo']; ?>
									<i class="fa-chevron-down material-icons">expand_more</i>
								</div>

									<?php // apliciones que corresponde al grupo de cabecera ?>

								<ul class="submenu">
									<?php  
										while ($aplicacion=mysqli_fetch_array($aplicaciones)) { ?>
											<li><a href="<?php echo '../'.$aplicacion['url']; ?>" class="item" data-url="<?php echo $aplicacion['aplicacion']; ?>"><?php echo $aplicacion['aplicacion']; ?></a></li>
									<?php } ?>
								</ul>
							</li>
					<?php }
				}
			 ?>

		</ul>

	</div>
			
</div>

<div class="lienzo-menu"></div>