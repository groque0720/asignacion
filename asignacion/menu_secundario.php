<div class="menu-secundario">
	<ul class="menu lista-menu">
<!-- IDUSUARIO = 47 DON VARGAS - TIENE SU PROPIO MENU-->
	<?php if ($_SESSION['id']==47): ?>
				<li style="border: 1px solid #ccc; border-radius: 3px;"><a href="planilla_asignacion_pdf.php" target="_blank"> <span></span>Asignación</a></li>
				<li></li>
				<li style="border: 1px solid #ccc; border-radius: 3px;"><a href="stock_real_sin_vender_pdf.php" target="_blank" > <span></span>Stock</a></li>
				<li></li>
				<li style="border: 1px solid #ccc; border-radius: 3px;"><a href="usados_pdf.php" target="_blank" > <span></span>Usados</a></li>
				<li></li>
				<li style="border: 1px solid #ccc; border-radius: 3px;" ><a href="falta_cancelacion_mas_diez_dias.php" target="_blank" > <span></span>Sin Cancelar</a></li>
				<li></li>
				<li style="border: 1px solid #ccc; border-radius: 3px;"><a href="stock_siniestrada_suc_pdf.php" target="_blank" > <span></span>Siniestradas</a></li>
	<?php endif ?>
	<!-- MENU CUANDO NO ES DE DON VARGAS -->
	<?php if ($_SESSION['id']!=47): ?>

		<?php if ($_SESSION["idperfil"]==14) {?>
			<li><a href="#" class="item_link"><span class="icon-car">  </span>Asignación</a>
				<ul>
					<li class=""><a href="#" class="item_link" data-id="1"><span class="icon-carga-uno"> </span>Nuevo</a></li>
					<li class=""><a href="#" class="item_link" data-id="2"><span class="icon-carga-masiva">  </span>Carga Masiva</a></li>
					<li class=""><a href="#" class="item_link" data-id="3"><span class="icon-cambio">  </span>Cambiar</a></li>
					<li class=""><a href="excel_planilla_asignacion.php" class="" data-id=""><span class="icon-download">  </span>En Excel</a></li>
				</ul>
			</li>

			<li><a href="#" class="item_link"><span class="icon-ambulance">  </span>Alertas</a>
				<ul>
<!-- 					<li class=""><a href="#" class="item_link" data-id="reservas_sin_sena"><span class="icon-usd"> </span>Reservas Sin Seña</a></li>
					<li class=""><a href="#" class="item_link" data-id="reservas_nro_unidad_duplicada"><span class="icon-code-fork"> </span>Unidades Duplicadas</a></li>
					<li class=""><a href="#" class="item_link" data-id="reservas_sin_nro_unidad"><span class="icon-chain-broken"> </span>Reservas Sin N° Unidad</a></li> -->
					<li class=""><a href="#" class="item_link" data-id="reservas_levantadas"><span class="icon-chevron-up"> </span>Reservas Levantadas</a></li>
				</ul>
			</li>
			<!-- <li class=""><a href="../ventas/web/recursos_dyv_toyota.php" target="_blank"><span class="icon-usd">  </span>Costos / Recursos</a></li> -->

		<?php } ?>
			<li><a href="#" class="item_link"><span class="icon-calendar">  </span>Agendas</a>
				<ul>
					<li><a href="" class="item_link icon-calendar" data-id="agenda_entregas"> <span></span>Agenda de Entrega</a></li>
					<li class=""><a href="../agenda_test_drive" target="_blank"><span class="icon-calendar">  </span>Agenda TD</a></li>
				</ul>
			</li>
			<li><a href="#" class="item_link"><span class="icon-car">  </span>Reportes</a>
				<ul>
					<li class=""><a href="excel_planilla_asignacion.php" class="" data-id=""><span class="icon-download">  </span>En Excel</a></li>
					<li class=""><a href="excel_planilla_asignacion_rosadas.php" class="" data-id=""><span class="icon-download">  </span>En Excel - Rosadas</a></li>
					<li class=""><a href="excel_planilla_asignacion_verdes.php" class="" data-id=""><span class="icon-download">  </span>En Excel - Verdes</a></li>
					<li class=""><a href="excel_planilla_asignacion_amarillas.php" class="" data-id=""><span class="icon-download">  </span>En Excel - Amarillas</a></li>
					<li class=""><a href="excel_planilla_asignacion_naranjas.php" class="" data-id=""><span class="icon-download">  </span>En Excel - Naranjas (Agosto)</a></li>
					<li class=""><a href="excel_planilla_asignacion_corollas_celeste.php" class="" data-id=""><span class="icon-download">  </span>En Excel - Corolla Celestes (Dic 20)</a></li>
					<li class=""><a href="excel_planilla_asignacion_preventa_sep20.php" class="" data-id=""><span class="icon-download">  </span>En Excel - Preventa Sep 2020</a></li>
					<li><a href="planilla_asignacion_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Asignación Gral.</a></li>
					<li><a href="planilla_asignacion_nc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Reservas No Confirmadas</a></li>
					<li><a href="planilla_asignacion_stock_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Costo Asignación Stock</a></li>

					<li><a href="falta_cancelacion_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Llegadas Sin Cancelar</a></li>
					<li><a href="falta_cancelacion_mas_diez_dias.php" target="_blank" class="icon-file-pdf-o"> <span></span>Llegadas Sin Cancelar + 10 dias</a></li>
					<li><a href="stock_por_suc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock x Suc.</a></li>
					<li><a href="stock_traslado_suc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Traslados x Suc.</a></li>
					<li><a href="en_viaje_pdf.php" target="_blank" target="_blank" class="icon-file-pdf-o" > <span></span>En Viaje</a></li>
					<li><a href="pendientes_tasa_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Pendiente TASA</a></li>
					<li><a href="stock_siniestrada_suc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Siniestradas</a></li>
					<li><a href="#" class="item_link icon-outdent" data-id="ultimas_entregas"> <span></span>Últimas Entregas</a></li>
					<li><a href="llegadas_porllegar_mesactual.php" class="icon-file-pdf-o" data-id=""> <span></span>Por Llegar Mes Actual</a></li>

				</ul>
			</li>
			<li><a href="#" class="item_link"><span class="icon-car">  </span>Info Stock</a>
				<ul>


					<li><a href="stock_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM</a></li>
					<li><a href="stock_real_sin_vender_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM - Real Sin Vender</a></li>
					<li><a href="stock_llegadas_sin_confirmar_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM - Físico Sin Vender</a></li>
					<li><a href="stock_real_sin_vender_con_interno.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM - Sin Vender con interno</a></li>
					<li><a href="stock_real_sin_vender_mes_ant_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock 0 KM (Mes Ant.)</a></li>
					<li><a href="stock_gral_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock Gral. (0km+TPA)</a></li>
					<!-- <li><a href="stock_gral_ma_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock Gral. (Mes Ant.)</a></li> -->
					<li><a href="stock_efv_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock EFV</a></li>
					<li><a href="stock_por_suc_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Stock x Suc.</a></li>

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
	<?php endif ?>
			<li><a href="#" class="item_link">USADOS</a>
				<ul>

					<li><a href="usados_pdf.php" target="_blank" class="icon-file-pdf-o"> <span> </span>Planilla Gral.</a></li>
					<li><a href="usados_certificados_pdf.php" target="_blank" class="icon-file-pdf-o"> <span> </span>Planilla Físico</a></li>
				</ul>
			</li>
	</ul>
		<!-- <div class="clearfix"></div> -->
	<div>
		<span class="icon-search">  </span><input type="text" placeholder="Buscar en Asignación" id="texto_buscar" size="30">
	</div>

</div>
<script src="js/menu-secundario.js"></script>
