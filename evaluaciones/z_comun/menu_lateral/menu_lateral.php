<?php

	$SQL="SELECT * FROM aplicaciones_grupos WHERE activo=1";
	$grupos=mysqli_query($con, $SQL); //Grupos de aplicaciones

 ?>

<div class="menu-lateral menu-lateral-oculto" id="wrapper">

	<div class="scrollbar" id="style-3">
		<ul id="accordion" class="accordion">
			<li>
				<div class="link">
					<i class="material-icons">
						<img src="../z_comun/imagenes/<?php echo $grupo['icono']; ?>" alt="">
					</i>
					<a href="../usuario">Cambiar Contraseña</a>
					<!-- <i class="fa-chevron-down material-icons">expand_more</i> -->
				</div>
			</li>
		</ul>
	</div>

</div>

<div class="lienzo-menu"></div>