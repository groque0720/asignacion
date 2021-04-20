<fieldset>
	<div id="firma" style="width: 97%; text-align:right; margin:5px auto;">

		<div style="width: 36%; height:20px; text-align:left; float:left; color:red;">

			<label>OFRECIO TD:</label>
			<select name="ofreciotd" id="ofreciotd">
				<option value="0" <?php  if ($reserva['ofreciotd'] == 0 ) { echo "selected"; } ?>>No</opcion>
				<option value="1" <?php  if ($reserva['ofreciotd'] == 1 ) { echo "selected"; } ?>>Si</opcion>
			</select>

			<label>REALIZO TD:</label>
			<select name="realizotd" id="realizotd">
				<option value="0" <?php  if ($reserva['realizotd'] == 0 ) { echo "selected"; } ?>>No</opcion>
				<option value="1" <?php  if ($reserva['realizotd'] == 1 ) { echo "selected"; } ?>>Si</opcion>
			</select>
		</div>
		<div id="porqueno" style="float:left; height:20px; text-align:left; float:left; color:red;">
			<label for="">PORQUE:</label>
			<input type="text" name="porque_no" id="porque_no" value="<?php echo $reserva['porque_no']; ?>" size="55" required>
		</div>

	</div>
</fieldset>

<fieldset>
	<div id="firma" style="width: 97%; margin:5px auto; display: flex; justify-content: space-between;">
		<?php
			$fecha_reserva = strtotime($reserva['fecres']);
			$fecha_tema_seguro = strtotime("2021/04/20");
		 ?>
		<div style="width: 60%; display: flex; <?php if ($fecha_reserva < $fecha_tema_seguro) { echo "display: none"; } ?>">
			<div style="color: red; width: 50%">
				<label>OFRECIO SEGURO:</label>
				<select name="ofreciosg" id="ofreciosg">
					<option value="0" <?php  if ($reserva['ofreciosg'] == 0 ) { echo "selected"; } ?>>No</opcion>
					<option value="1" <?php  if ($reserva['ofreciosg'] == 1 ) { echo "selected"; } ?>>Si</opcion>
				</select>
			</div>
			<div style="color: red; width: 50%">
				<label>TOMARA SEGURO:</label>
				<select name="tomarasg" id="tomarasg">
					<option value="0" <?php  if ($reserva['tomarasg'] == 0 ) { echo "selected"; } ?>>No</opcion>
					<option value="1" <?php  if ($reserva['tomarasg'] == 1 ) { echo "selected"; } ?>>Si</opcion>
					<option value="2" <?php  if ($reserva['tomarasg'] == 2 ) { echo "selected"; } ?>>No sabe aún</opcion>
				</select>
			</div>
		</div>

		<div style="width: 30%; text-align:right; margin:5px auto; display: flex;">
			<div style="height:20px;">
				FIRMA:
			</div>
			<div style="width:100%; height:20px; border-bottom: 1px dotted #000;">
			</div>

		</div>

	</div>
</fieldset>

