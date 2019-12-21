<div class="menu-secundario">

	<ul class="lista-menu">
	<?php if ($_SESSION["idperfil"]==14) {?>
		<li class="item-menu"><a href="#" class="item_link" data-id="1"><span class="icon-carga-uno"> </span>Nuevo</a></li>
		<li class="item-menu"><a href="#" class="item_link" data-id="2"><span class="icon-carga-masiva">  </span>Carga Masiva</a></li>
		<li class="item-menu"><a href="#" class="item_link" data-id="3"><span class="icon-cambio">  </span>Cambiar Unidad</a></li>
		<li class="item-menu"><a href="planilla_asignacion_pdf.php"  target="_blank"  class="" data-id="6"><span class="icon-print" >  </span >Planilla Completa</a></li>
	<?php } ?>
		<li class="item-menu"><a href="#" class="item_link" data-id="4"><span class="icon-stock">  </span>Stock</a></li>
		<li class=""><span class="icon-filter">  
			<select class="sub-filtro" name="" id="">
				<option value="">Filtro Rápido</option>
				<option value="1">En Viaje</option>
				<option value="2">Pendientes TASA</option>
			</select>
			</span>
		</li>

	</ul>
	<div>
		<span class="icon-search">  </span><input type="text" placeholder="Buscar" id="texto_buscar" size="30">
	</div>
	
</div>
<script src="js/menu-secundario.js"></script>