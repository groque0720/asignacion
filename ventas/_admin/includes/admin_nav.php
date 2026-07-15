<nav id="menu_lateral">

		<ul>	
			<a href="sucursales.php"><li>Sucursales</li></a>
			<?php // Sólo el admin administra usuarios (ver includes/security_usuarios.php).
			if ((int) $_SESSION["idperfil"] === 1) { ?>
				<a href="usuarios.php"><li>Usuarios</li></a>
			<?php } ?>
			<a href="perfiles.php"><li>Perfiles</li></a>
			<a href="tipos.php"><li>Tipos de Veh&iacute;</li></a>
			<a href="grupos.php"><li>Grupos</li></a>
			<a href="modelos.php"><li>Modelos</li></a>
			<a href="precios.php"><li>Lista de Precio</li></a>
			<a href="codigos.php"><li>C&oacute;digos</li></a>
			<a href="creditos.php"><li>Tipos Cr&eacute;ditos</li></a>
			<a href="financieras.php"><li>Financieras</li></a>
		</ul>

	</nav>