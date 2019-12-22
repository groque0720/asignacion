<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");


$SQL="SELECT * FROM regrecepcion WHERE idcontacto =".$_POST["id_cont"];
$res=mysqli_query($con, $SQL);
$cont=mysqli_fetch_array($res);
?>

<input type="date" id="fecha" name="fecha" value="<?php echo $cont['fecha']; ?>" >
<select name="sector" id="sector" required>
	<option value=""></optsion>
	<option value="Ventas" <?php  if ($cont["sector"] == "Ventas") { echo "selected"; } ?>>Ventas</option>
	<option value="Servicios" <?php  if ($cont["sector"] == "Servicios") { echo "selected"; } ?>>Servicios</option>
	<option value="Respuestos" <?php  if ($cont["sector"] == "Respuestos") { echo "selected"; } ?>>Respuestos</option>
	<option value="Plan de Ahorro" <?php  if ($cont["sector"] == "Plan de Ahorro") { echo "selected"; } ?>>Plan de Ahorro</option>
	<option value="Otros" <?php  if ($cont["sector"] == "Otros") { echo "selected"; } ?>>Otros</option>
</select>
<input id="cliente" name="cliente" type="text" placeholder="Cliente" value="<?php echo $cont['cliente']; ?>" required>
<select name="med_cont" id="med_cont" required>
	<option value=""></option>
	<option value="Presencial" <?php  if ($cont["acercamiento"] == "Presencial") { echo "selected"; } ?>>Presencial</option>
	<option value="Telefónico" <?php  if ($cont["acercamiento"] == "Telefónico") { echo "selected"; } ?>>Telefónico</option>
	<option value="E-mail" <?php  if ($cont["acercamiento"] == "E-mail") { echo "selected"; } ?>>E-mail</option>
	<option value="Otros" <?php  if ($cont["acercamiento"] == "Otros") { echo "selected"; } ?>>Otros</option>
</select>
<input id="asignado" name="asignado" type="text" placeholder="Enviado a" value="<?php echo $cont['asesor']; ?>" required>
<input id="telefono" name="telefono" type="text" placeholder="Teléfono de Contacto" value="<?php echo $cont['telefono']; ?>" required>

<input id="email" name="email" type="email" placeholder="Correo Electrónio" value="<?php echo $cont['email']; ?>" required>
<select name="seg_siac" id="seg_siac" required>
	<option value=""></option>
	<option value="0" <?php  if ($cont["seguimiento"] == 0) { echo "selected"; } ?>>No</option>
	<option value="1" <?php  if ($cont["seguimiento"] == 1) { echo "selected"; } ?>>SI</option>
</select>

<textarea name="obs" id="obs" cols="30" rows="10" ><?php echo $cont['observacion']; ?></textarea>


	<?php  mysqli_close($con); ?>

