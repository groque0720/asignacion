<div class="menu-secundario" style="width: 90%">

	<ul class="menu lista-menu" style="display: flex;">

		<li class=""><a href="#" class="item_link" data-id="1"><span class="icon-carga-uno"> </span>Nuevo</a></li>
		<li style="display: flex; justify-content: flex-end; align-items: center; margin-left: 15px; background: #E3E0E0; padding: 0 5px 0 5px; border-radius: 3px;">
			<label for="" style="height: 20px; display: flex; align-items: center; margin-right: 10px;">Reporte:</label>
			<input type="date" id="fecha_filtro" style="height: 20px;" value="<?php echo date("Y-m-d"); ?>">
			<button style="margin-left: 10px;" id="btn_filtro_fecha">Registros</button>
			<button style="margin-left: 10px;" id="btn_filtro_fecha_nc">NC Precio</button>
		</li>

		<!-- <li><a href="#" class="item_link"><span class="icon-car">  </span>Convencional</a>
			<ul>
				<li><a href="planilla_asignacion_pdf.php" target="_blank" class="icon-file-pdf-o"> <span></span>Asignación Gral.</a></li>
			</ul>
		</li> -->

		<!-- <li class=""><a href="#" class="item_link" data-id="4"><span class="icon-filter">  </span>Filtro</a> -->
	</ul>
		<!-- <div class="clearfix"></div> -->
	<?php
		if ($_SESSION["idsuc"]==2) { ?>
		<div style="margin-right: : 30px; width: 230px;">
			<span>Sucursal Activa:</span>
			<select name="select_suc" id="select_suc">
				<?php
					$SQL="SELECT * FROM sucursales";
					$sucursales = mysqli_query($con, $SQL);
					while ($suc=mysqli_fetch_array($sucursales)) { ?>
						<option value="<?php echo $suc['idsucursal']; ?>" <?php if ($_SESSION["idsuc"]==$suc['idsucursal']) {
							echo 'selected';
						} ?>><?php echo $suc['sucursal']; ?></option>
					<?php } ?>
			</select>
		</div>
	<?php }	 ?>




	<div>

		<span class="icon-search">  </span><input type="text" placeholder="Buscar" id="texto_buscar" size="30">

	</div>

</div>
<script src="js/menu-secundario.js"></script>
