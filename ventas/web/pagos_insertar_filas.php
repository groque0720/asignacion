<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");
	@session_start();
	$id_usuario = $_SESSION["id"];

	$idrecord=$_POST["idreserva"];
	$nrores=$_POST["idreserva"];

$add="";

	if ($_POST["tipo_pago"]==3) {
		$add=" cancelada = 1, ";
	}else{
		$add=" cancelada = 0, ";
	}

$SQL="UPDATE reservas SET";
$SQL.=$add;
$SQL .="  estadopago =".$_POST["tipo_pago"]." ";
$SQL .=" WHERE idreserva =".$_POST["idreserva"];
mysqli_query($con, $SQL);


	if ($_POST["mov"] == 1) { // insertar
		$SQL="INSERT INTO pagos_lineas(idreserva, fecha, tipo, modo, financiera, nrorecibo, monto, obs, id_usuario) VALUES
		(".$_POST["idreserva"].", '".$_POST["fecha"]."', ".$_POST["tipo_pago"].", ".$_POST["modo_pago"].", ".$_POST["finan"].",'".$_POST["nrorecibo"]."',".$_POST["monto_pago"].",'".$_POST["obs"]."',". $id_usuario.")";
		mysqli_query($con, $SQL);
	};

	if ($_POST["mov"] == 2){ //editar

		$SQL = "UPDATE pagos_lineas SET";
		$SQL .=" fecha ='".$_POST["fecha"]."', ";
		$SQL .=" tipo =".$_POST["tipo_pago"].", ";
		$SQL .=" modo =".$_POST["modo_pago"].", ";
		$SQL .=" financiera =".$_POST["finan"].", ";
		$SQL .=" nrorecibo ='".$_POST["nrorecibo"]."', ";
		$SQL .=" monto =".$_POST["monto_pago"].", ";
		$SQL .=" obs ='".$_POST["obs"]."' ";
		$SQL .=" WHERE idpago =".$_POST["nrolin"];
		mysqli_query($con, $SQL);
	};

	if ($_POST["mov"] == 3){ //Eliminar
		$SQL="DELETE  FROM pagos_lineas WHERE idpago =".$_POST["nrolin"];
		mysqli_query($con, $SQL);

	};

	$totalop = ($_POST["total"]*1000)+0;
//-------------------------------------------------------------------------------------------------------------------
if ($_POST["tipo_pago"]==3 AND $_POST["mov"] != 3) {

$SQL="SELECT * FROM reservas WHERE idreserva=".$_POST["idreserva"];
$res=mysqli_query($con, $SQL);
if (empty($res)) {$reserva['idreserva']="";}else{ $reserva=mysqli_fetch_array($res);}


$SQL="SELECT * FROM grupos WHERE idgrupo=".$reserva["idgrupo"];
$gru=mysqli_query($con, $SQL);
if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}


$SQL="SELECT * FROM modelos WHERE idmodelo=".$reserva["idmodelo"];
$mod=mysqli_query($con, $SQL);
if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}


$SQL="SELECT * FROM usuarios WHERE idusuario = ".$reserva["idusuario"];
$usuarios=mysqli_query($con, $SQL);
if (empty($usuarios)) {$usuario['email']="";}else{ $usuario=mysqli_fetch_array($usuarios);}


$SQL="SELECT * FROM clientes WHERE idcliente = ".$reserva["idcliente"];
$clientes=mysqli_query($con, $SQL);
if (empty($clientes)) {$cliente['email']="";}else{ $cliente=mysqli_fetch_array($clientes);}


// Carga de las notificaciones
$hora=date( 'H:i');

$SQL="SELECT * FROM notificacionespara WHERE tiponot=6";
$res=mysqli_query($con, $SQL);

while ($not=mysqli_fetch_array($res)) {

$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, idpago, interno, modelo, cliente, asesor, visto, obs )";
$SQL .=" VALUES (6,'".date("Y-m-d")."','$hora','".$not["idusuario"]."','".$reserva["compra"]."','".$_POST["idreserva"]."','".$reserva["idcliente"]."','".$reserva["interno"].$reserva["internou"]."','".$grupo['grupo']." ".$modelo['modelo']."".$reserva["detalleu"]."','".$cliente["nombre"]."','".$usuario["nombre"]."',0,'-')";
mysqli_query($con, $SQL);

};

$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, idpago, interno, modelo, cliente, asesor, visto, obs )";
$SQL .=" VALUES (6,'".date("Y-m-d")."','$hora','".$reserva["idusuario"]."','".$reserva["compra"]."','".$_POST["idreserva"]."','".$reserva["idcliente"]."','".$reserva["interno"].$reserva["internou"]."','".$grupo['grupo']." ".$modelo['modelo']."".$reserva["detalleu"]."','".$cliente["nombre"]."','".$usuario["nombre"]."',0,'-')";
mysqli_query($con, $SQL);

// Carga de las notificaciones

$email = "online@dyv-online.com.ar";

$titulo="UNIDAD CANCELADA - ".$cliente["nombre"]." (".$usuario["nombre"].")";

$mensaje = "SE NOTIFICA LA CANCELACION DE LA SIGUIENTE OPERACION: \n";
$mensaje .= "CLIENTE: '".$cliente["nombre"]."'\n";
$mensaje .= "ASESOR: '".$usuario["nombre"]."'\n";
$mensaje .= "GRUPO: '".$grupo["grupo"]."'\n";
$mensaje .= "MODELO: '".$modelo["modelo"]."'\n";
$mensaje .= "---------------------------------\n";
//$mensaje .= $usuario["email"]."\n";
$mensaje .= "VER DETALLE DE PAGOS \n";
$mensaje .= "http://dyvsa.com.ar/web/pago.php?IDrecord=".$cliente["idcliente"]."\n";
$mensaje .= "---------------------------------\n";
$mensaje .= "VER RESERVA \n";
$mensaje .= "http://dyvsa.com.ar/web/reserva_web.php?IDrecord=".$_POST["idreserva"];


$parados = "lauraderka@derkayvargas.com.ar, rukyguerra@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar, danielvicentin@derkayvargas.com.ar,".$usuario["email"];


$headers = 'From: online@dyvsa.com.ar' . "\r\n" .
    		'Reply-To: online@dyvsa.com.ar' . "\r\n" .
    		'X-Mailer: PHP/' . phpversion();

mail($parados, $titulo, $mensaje, $headers);
// mail($para, $titulo, $mensaje, $headers);
}
//--------------------------------------------------------------------------------------------------------------------------
?>
<script type="text/javascript" src="../js/abm_pagos.js"></script>

<?php include("pago_cuerpo.php"); ?>

<script type="text/javascript">

var debe ="<?php echo number_format($debe, 2, ',','.'); ?>";
var monto_pagado ="<?php echo number_format($pagado, 2, ',','.'); ?>";
$("#pagado").val(monto_pagado);
$("#acancelar").val(debe);

</script>
<?php  mysqli_close($con);  ?>