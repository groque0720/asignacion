<script src="../js/noti_res_nueva.js"></script>

<div class="barra_btns">
	<!-- Cominezo - Realización de Paginación -->

	<input id="inicio" name="inicio" type="hidden" value="<?php echo $_GET["inicio"]; ?>">
	<input id="pagina" name="pagina" type="hidden" value="<?php echo $_GET["pagina"]; ?>">
	<?php

		include("../funciones/func_mysql.php");
		conectar();
		$SQL="SELECT * FROM notificaciones WHERE tiponot = 1 AND idusuario =".$_GET["id"]." AND borrar = 0 ORDER BY idnotificaciones DESC";
		$res=mysqli_query($con, $SQL);
		$parametro_reg=50;// si se cambia el valor $parametro_reg hay que cambiar en el jquery para que funcione correctamanet
		$cantidad_reg = mysqli_num_rows($res);
		$total_paginas=ceil($cantidad_reg/$parametro_reg);
		echo "Pagina ".$_GET["pagina"]." de ".$total_paginas;
		echo '<a class="flecha fizq" href=""><img src="../imagenes/izq.gif" border="0"></a>';
	 	for ($i=1; $i < $total_paginas+1; $i++) {
			if ($_GET["pagina"]!=$i) {
				echo ' '.'<a class="indice" data-id="'.$i.'" href="">'.$i.'</a>'.' ';
			}else{
				echo '<span class="pag_sel">'.$i.'</span> ';
			};
		};
		echo '<a class="flecha fder" href=""><img src="../imagenes/der.gif" border="0"></a>';
	?>
	<input id="total_paginas" name="total_paginas" type="hidden" value="<?php echo $total_paginas; ?>">
	<!-- Fin - Realización de Paginación -->

</div>


	<table rules="all" border="1" id="tabla_res">

		<?php
			$SQL="SELECT * FROM notificaciones WHERE tiponot = 1 AND idusuario =".$_GET["id"]." AND borrar = 0 ORDER BY idnotificaciones DESC LIMIT ".$_GET["inicio"].", $parametro_reg";
			$res=mysqli_query($con, $SQL);
		?>

		<thead>
			<tr>
				<td width="4%">Nro Res.</td>
				<td width="5%">Fecha</td>
				<td width="3%">Hora</td>
				<td width="5%">Compra</td>
				<td width="4%">Int.</td>
				<td width="17%">Modelo</td>
				<td width="17%">Cliente</td>
				<td width="6%">Asesor</td>
				<td width="7%">Panel</td>
				<td width="2%" class="idnoticia">id</td>
			</tr>
		</thead>

		<tbody>
			<?php $nrofila=0; ?>
			<?php while ($noti_res=mysqli_fetch_array($res)) { ?>
			<?php $nrofila = $nrofila + 1; ?>
			<tr class="<?php if ($noti_res["visto"]==0) { echo "negrita";} ?>">
				<td ><?php echo $noti_res["idreserva"];?></td>
				<td ><?php echo cambiarformatofecha($noti_res['fechanot']);?> </td>
				<td ><?php echo cambiarformatohora($noti_res['hora']);?></td>
				<td ><?php echo $noti_res["compra"];?></td>
				<td ><?php if ($noti_res["interno"]!=0) { echo $noti_res["interno"];}else{echo "-";}?></td>
				<td ><?php echo $noti_res["modelo"];?></td>
				<td ><?php echo $noti_res["cliente"];?></td>
				<td ><?php echo $noti_res["asesor"];?></td>
				<td >
					<a href="reserva.php?IDrecord=<?php echo $noti_res['idreserva']; ?>" data-id="<?php echo $nrofila; ?>" class="ir_reserva" id="ir" target="_blank"></a>

					<?php if ($noti_res["visto"]==0) { ?>
						<a id="selvisto" class="novisto" data-id="<?php echo $nrofila; ?>" href=""></a>
					<?php }else{  ?>
						<a id="selvisto" class="visto" data-id="<?php echo $nrofila; ?>" href=""></a>
					<?php } ?>

					<a href="" class="borrar" id="borrar" data-id="<?php echo $nrofila; ?>"></a>
					<a href="<?php echo $noti_res["idusuario"]; ?>" class="seguimiento" id="seguimiento" data-id="<?php echo $noti_res['idreserva']; ?>"></a>

				</td>
				<td class="idnoticia"><?php echo $noti_res["idnotificaciones"];?></td>
			</tr>

			<?php } ?>
		</tbody>

	</table>

<?php mysqli_close($con); ?>