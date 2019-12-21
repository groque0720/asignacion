<?php
 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="SELECT * FROM pagos_lineas WHERE idpago =".$_POST["idlin"];
$pago_lineas=mysqli_query($con, $SQL);
$pago=mysqli_fetch_array($pago_lineas);
?>
<input type="date" id="fecha" name="fecha" value="<?php echo $pago["fecha"]; ?>">