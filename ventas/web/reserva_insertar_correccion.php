<?php
 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE reservas SET";
$SQL .="  corregir ='".$_POST["mensaje"]."' ";
$SQL .=" WHERE idreserva =".$_POST["nrores"];
mysqli_query($con, $SQL);

$SQL="SELECT * FROM grupos WHERE idgrupo=".$_POST["grupo"];
$gru=mysqli_query($con, $SQL);
if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

$SQL="SELECT * FROM modelos WHERE idmodelo=".$_POST["modelo"];
$mod=mysqli_query($con, $SQL);
if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}

echo " ".$_POST["mensaje"];


$email = "online@dyv-online.com.ar";
// $para = "ventas@derkayvargas.com.ar, arieljergus@derkayvargas.com.ar, lauraderka@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar, rukyguerra@derkayvargas.com.ar, juanfernandez@derkayvargas.com.ar, roquegomez@derkayvargas.com.ar";
// $para .= "juanverza@derkayvargas.com.ar, luisgutierrez@derkayvargas.com.ar, dariogonzalez@derkayvargas.com.ar, gomezroque@hotmail.com";
//$parauno = "ventas@derkayvargas.com.ar, arieljergus@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar";
$parados = "rukyguerra@derkayvargas.com.ar, lauraderka@derkayvargas.com.ar ".$_POST["email"];
$para = "federicorescala@derkayvargas.com.ar, arieljergus@derkayvargas.com.ar";

$headers = 'From: online@dyv-online.com.ar' . "\r\n" .
    		'Reply-To: online@dyv-online.com.ar' . "\r\n" .
    		'X-Mailer: PHP/' . phpversion();


	$titulo="CORRECCION DE SOLICITUD DE RESERVA - CLIENTE: ".$_POST["nombre"];

	$mensaje = "SE HA REALIZADO LA SIGUIENTE CORRECCION";
	$mensaje .=" Nro Reserva: ".$_POST["nrores"]."\n";
	$mensaje .= "UNIDAD: ".$_POST["compra"]."\n";
	$mensaje .= "ASESOR: ".$_POST["asesorres"]."\n";
	$mensaje .= "CLIENTE: ".$_POST["nombre"]."\n";
	$mensaje .= "UNIDAD: ".$grupo["grupo"]." - ".$modelo['modelo']." ".$_POST["detalleu"]."\n";
	$mensaje .= "ENTREGA: ".$_POST["mesentrega"]." - ".$_POST["anoentrega"]."\n";
	$mensaje .= "--------- \n";
	$mensaje .= "CORRECCION: ".$_POST["mensaje"]."\n";
	$mensaje .= "AUTOR: ".$_POST["usuario_a"]."\n";
	$mensaje .= "--------- \n";
	$mensaje .= "Link: http://dyv-online.com.ar/web/reserva.php?IDrecord=".$_POST["nrores"];

	mail($para, $titulo, $mensaje, $headers);
	mail($parados, $titulo, $mensaje, $headers);
	 mysqli_close($con);
//header("Location: asesores.php");
 ?>