<div class="menu-secundario">

	<ul class="menu lista-menu">
	<?php if ($_SESSION["idperfil"]==14) {?>
		<li class=""><a href="#" class="item_link" data-id="1"><span class="icon-carga-uno"> </span>Nuevo</a></li>
		<li class=""><a href="#" class="item_link" data-id="2"><span class="icon-carga-masiva">  </span>Carga Masiva</a></li>
		<li class=""><a href="#" class="item_link" data-id="3"><span class="icon-cambio">  </span>Cambiar</a></li>

		<li class=""><a href="../ventas/web/recursos_dyv_toyota.php" target="_blank"><span class="icon-usd">  </span>Costos / Recursos</a></li>
	<?php } ?>

		<li><a href="" class="item_link icon-calendar" data-id="agenda_entregas"> <span></span>Agenda de Entrega</a></li>
		<li class=""><a href="../agenda_test_drive" target="_blank"><span class="icon-calendar">  </span>Agenda TD</a></li>

		<li><a href="#" class="item_link"><span class="icon-car">  </span>Convencional</a>
			<ul>
				<li><a href="planilla_asignacion_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Asignación Gral.</a></li>
				<li><a href="planilla_asignacion_nc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Reservas No Confirmadas</a></li>
				<li><a href="planilla_asignacion_stock_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Asignación Stock</a></li>
				<li><a href="stock_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM</a></li>
				<li><a href="stock_ma_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM (Mes Ant.)</a></li>
				<li><a href="stock_gral_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock Gral. (0km+TPA)</a></li>
				<li><a href="stock_gral_ma_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock Gral. (Mes Ant.)</a></li>
				<li><a href="stock_efv_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock EFV</a></li>
				<li><a href="usados_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Usados</a></li>
				<li><a href="falta_cancelacion_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Llegadas Sin Cancelar</a></li>
				<li><a href="stock_por_suc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock x Suc.</a></li>
				<li><a href="stock_traslado_suc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Traslados x Suc.</a></li>
				<li><a href="en_viaje_pdf.php" target="_blank" target="_blank" class="icon-file-pdf-o" > <span></span>En Viaje</a></li>
				<li><a href="pendientes_tasa_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Pendiente TASA</a></li>
				<li><a href="#" class="item_link icon-outdent" data-id="ultimas_entregas"> <span></span>Últimas Entregas</a></li>
			</ul>
		</li>	

		<li><a href="#" class="item_link"><span class="icon-pinterest-p">  </span>Plan Ahorro</a>
			<ul>
				<li><a href="plan_ahorro.php" target="_blank" class="icon-outdent"><span> </span>Aplicación</a></li>
				<li><a href="plan_ahorro_planilla_asignacion_pdf.php" target="_blank" class="icon-file-pdf-o"> <span> </span>Asignación</a></li>
				<li><a href="plan_ahorro_stock_pdf.php" target="_blank" class="icon-file-pdf-o"> <span> </span>Stock</a></li>
			</ul>
		</li>

		<li class=""><a href="#" class="item_link" data-id="4"><span class="icon-filter">  </span>Filtro</a>
	</ul>
		<!-- <div class="clearfix"></div> -->
	<div>
		<span class="icon-search">  </span><input type="text" placeholder="Buscar" id="texto_buscar" size="30">
	</div>
	
</div>
<script src="js/menu-secundario.js"></script>
