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
	<div style="width: 97%; text-align:right; margin:5px auto;">
			<div style="width: 230px; height:20px; border-bottom: 1px dotted #000; float:right;">
			</div>

			<div style="width: 100px; height:20px; float:right;">
			FIRMA:	
			</div>
		</div>
	</div>	
</fieldset>