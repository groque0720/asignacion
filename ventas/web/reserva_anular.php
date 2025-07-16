<?php

 include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");


//echo $_GET["obser"]." ".$_GET["idres"];


$SQL="UPDATE reservas SET";
$SQL .="  nrounidad = null,";
$SQL .="  interno = '',";
$SQL .="  nroorden = '',";
$SQL .="  anulada = 1,";
// $SQL .="  modificaciones =' ".cambiarFormatoFecha(date("Y-m-d"))." ".$_POST["obs_cambio"]." Reserva Anulada: Ver Causa en Observaciones -   /    - ', ";
$SQL .="  obsanulada = ' ".cambiarFormatoFecha(date("Y-m-d"))." RESERVA ANULADA - CAUSA:".$_GET["obser"]."' ";
$SQL .=" WHERE idreserva =".$_GET["idres"];
mysqli_query($con, $SQL);

$SQL="SELECT
usuarios.nombre AS usuario,
grupos.grupo AS grupo,
clientes.nombre AS cliente,
reservas.idreserva as idreserva,
modelos.modelo AS modelo,
reservas.obsanulada AS obsanulada,
reservas.detalleu AS detalleu,
reservas.compra AS compra,
reservas.interno AS interno,
reservas.internou AS internou,
reservas.enviada as enviada
FROM
reservas
Inner Join clientes ON clientes.idcliente = reservas.idcliente
Inner Join usuarios ON reservas.idusuario = usuarios.idusuario
left Join grupos ON grupos.idgrupo = reservas.idgrupo
left Join modelos ON modelos.idmodelo = reservas.idmodelo
WHERE  idreserva = ".$_GET["idres"];

$reservas=mysqli_query($con, $SQL);
$res=mysqli_fetch_array($reservas);


if ($res["enviada"]>0) {

	//$email = "online@dyv-online.com.ar";
	// $para = "ventas@derkayvargas.com.ar, arieljergus@derkayvargas.com.ar, lauraderka@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar, rukyguerra@derkayvargas.com.ar, juanfernandez@derkayvargas.com.ar, roquegomez@derkayvargas.com.ar,";
	// $para .= "juanverza@derkayvargas.com.ar, luisgutierrez@derkayvargas.com.ar, dariogonzalez@derkayvargas.com.ar, gomezroque@hotmail.com";
	//$parauno = "lauraderka@derkayvargas.com.ar, ventas@derkayvargas.com.ar, arieljergus@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar, rukyguerra@derkayvargas.com.ar";


	//$headers = 'From: online@dyv-online.com.ar' . "\r\n" .
    		'Reply-To: online@dyv-online.com.ar' . "\r\n" .
    		'X-Mailer: PHP/' . phpversion();

	//$titulo="ANULACION DE RESERVA - ".$res["cliente"]."(".$res["usuario"].")";

	//$mensaje = "SE ANULO RESERVA -".$res["cliente"]."(".$res["usuario"].")\n";
	//$mensaje .="\n";
	//$mensaje .=" Nro Reserva : ".$_GET["idres"]."\n";
	//$mensaje .= "UNIDAD: ".$res["compra"]."\n";
	//$mensaje .= "ASESOR: ".$res["usuario"]."\n";
	//$mensaje .= "CLIENTE: ".$res["cliente"]."\n";
	//$mensaje .= "UNIDAD: ".$res["grupo"]." - ".$res['modelo']." ".$res["detalleu"]."\n";
	//$mensaje .= " \n";
	//$mensaje .= "CAUSA DE ANULACION:".$_GET["obser"]."\n";
	//$mensaje .= "Link: http://dyvsa.com.ar/web/reserva_web.php?IDrecord=".$_GET["idres"];

	//mail($parauno, $titulo, $mensaje, $headers);

	// mail($parados, $titulo, $mensaje, $headers);

		// Carga de las notificaciones

	$hora=date( 'H:i');

	$SQL="SELECT * FROM notificacionespara WHERE tiponot=3";
	$resul=mysqli_query($con, $SQL);



	while ($not=mysqli_fetch_array($resul)) {

	echo $not["idusuario"].'<br>';

	$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, interno, modelo, cliente, asesor, visto, obs )";
	$SQL .=" VALUES (3,'".date("Y-m-d")."','$hora','".$not["idusuario"]."','".$res["compra"]."','".$_GET["idres"]."','".$res["interno"].$res["internou"]."','".$res['grupo']." ".$res['modelo']."".$res["detalleu"]."','".$res["cliente"]."','".$res["usuario"]."',0,'".$_GET["obser"]."')";


	mysqli_query($con, $SQL);
	};

	// Carga de las notificaciones

	//if ($res["compra"]="usado") {
		//$para="arielgutierrez@derkayvargas.com.ar";
		//mail($para, $titulo, $mensaje, $headers);
	//}

}
 mysqli_close($con);
header("Location: asesores.php");

 ?>