<?php
 include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="SELECT * FROM pagos_lineas WHERE idpago =".$_POST["idlinea"];
$result=mysqli_query($con, $SQL);
$lineas=mysqli_fetch_array($result);

?>

 <div class="fila">
	<label for="fecha">Fecha:</label>
	<input type="date" id="fecha" value="<?php echo $lineas["fecha"]; ?>" name="fecha">
</div>
<hr>

<div class="fila">

	<label for="tipo_pago">Tipo de Pago</label>

	<?php
	$SQL="SELECT * FROM pagos_tipos";
	$res=mysqli_query($con, $SQL);
	 ?>
	<select id="tipo_pago" name="tipo_pago">
		<option value="0" style="color:#ccc; background: red"></option>
		<?php while ($tipo=mysqli_fetch_array($res)) {?>
		<option value="<?php echo $tipo["idtipopago"] ?>"><?php echo $tipo["tipopago"] ?></option>
		<?php } ?>
	</select>

	<label for="tipo_pago">Modo de Pago</label>

	<?php
	$SQL="SELECT * FROM pagos_modos";
	$res=mysqli_query($con, $SQL);
	 ?>
	<select id="modo_pago" name="modo_pago" >
		<option value="0" style="color:#ccc; background: red"></option>
		<?php while ($modo=mysqli_fetch_array($res)) {?>
		<option value="<?php echo $modo["idpagomodo"] ?>"><?php echo $modo["modo"] ?></option>
		<?php } ?>
	</select>
</div>
<hr>
<div class="fila">
	<label for="financiera">Financiera:</label>
	<?php
	$SQL="SELECT * FROM financieras";
	$res=mysqli_query($con, $SQL);
	 ?>

	<select id="financiera" name="financiera" >
		<option value="0" style="color:#ccc; background: red"></option>
		<?php while ($fi=mysqli_fetch_array($res)) {?>
		<option value="<?php echo $fi["idfinanciera"]; ?>"><?php echo $fi["financiera"]; ?></option>
		<?php } ?>
	</select>
</div>
<hr>
<div class="fila">

	<label for="monto_pago">Monto:</label>
	<input type="text" id="monto_pago" name="monto_pago" value="0" size="10" style="text-align:right;">
	<label for="nrorecibo">Nro Recibo:</label>
	<input type="text" id="nrorecibo" name="nrorecibo" size="10" >

</div>
<hr>
	<textarea name="observacion" id="observacion" cols="30" rows="5" placeholder="observaci&oacute;n" style="width:100%;"></textarea>
