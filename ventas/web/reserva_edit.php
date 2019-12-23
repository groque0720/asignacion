<?php
 include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");
extract($_POST);

// var_dump($_POST);


$SQL = "UPDATE entregausado SET";
$SQL .=" marca ='".$_POST["marcau"]."', ";
$SQL .=" tipo ='".$_POST["tipou"]."', ";
$SQL .=" modelo ='".$_POST["modelou"]."', ";
$SQL .=" color ='".$_POST["coloru"]."', ";
$SQL .=" anio ='".$_POST["aniou"]."', ";
$SQL .=" dominio ='".$_POST["dominio"]."', ";
$SQL .=" km ='".$_POST["km"]."', ";
$SQL .=" info ='".$_POST["info"]."' ";
$SQL .=" WHERE identregau =".$_POST["identregau"];
mysqli_query($con, $SQL);



$SQL = "UPDATE clientes SET";
$SQL .=" nombre ='".$_POST["nombre"]."', ";
$SQL .=" sexo ='".$_POST["sexo"]."', ";

if ($_POST["fecnac"]!='') {
	$SQL.=" fecnac = '".$_POST["fecnac"]."', ";
}else{
	$SQL.=" fecnac = null ,";
}

//$SQL .=" fecnac ='".$_POST["fecnac"]."', ";
$SQL .=" edad ='".$_POST["edad"]."', ";
$SQL .=" tipodoc ='".$_POST["tipodoc"]."', ";
$SQL .=" nrodoc ='".$_POST["nrodoc"]."', ";
$SQL .=" cuil ='".$_POST["cuil"]."', ";
$SQL .=" ocupacion ='".$_POST["ocupacion"]."', ";
$SQL .=" estadocivil ='".$_POST["estadocivil"]."', ";
$SQL .=" grupofamiliar ='".$_POST["grupofamiliar"]."', ";
$SQL .=" canthijos ='".$_POST["canthijos"]."', ";
$SQL .=" direccion ='".$_POST["direccion"]."', ";
$SQL .=" localidad ='".$_POST["localidad"]."', ";
$SQL .=" provincia ='".$_POST["provincia"]."', ";
$SQL .=" mail ='".$_POST["mail"]."', ";
$SQL .=" tfijo ='".$_POST["tfijo"]."', ";
$SQL .=" tcelu ='".$_POST["tcelu"]."', ";
$SQL .=" prefcontacto='".$_POST["prefcontacto"]."', ";
$SQL .=" ctoyota ='".$_POST["ctoyota"]."'";
$SQL .=" WHERE idcliente =".$_POST["idcliente"];
mysqli_query($con, $SQL);



$SQL="UPDATE reservas SET";
$SQL .="  observacion ='".$_POST["observacion"]."', ";
$SQL .="  compra='".$_POST["compra"]."', ";
$SQL .="  nrounidad='".$_POST["nrounidad"]."', ";
// $SQL .="  fecres='".$_POST["fecres"]."', ";
$SQL .="  lugarventa ='".$_POST["lugarventa"]."', ";
$SQL .="  factura='".$_POST["factura"]."', ";
$SQL .="  fecult='".date("Y-m-d")."', ";
$SQL .="  tipoprecio='".$_POST["tipoprecio"]."', ";
$SQL .="  condicionpago='".$_POST["condicionpago"]."', ";
$SQL .="  mesentrega='".$_POST["mesentrega"]."', ";
$SQL .="  anoentrega='".$_POST["anoentrega"]."', ";
$SQL .="  marca='".$_POST["marca"]."', ";
$SQL .="  idtipo='".$_POST["tipo"]."', ";
$SQL .="  idgrupo='".$_POST["grupo"]."', ";
$SQL .="  idmodelo='".$_POST["modelo"]."', ";
$SQL .="  color='".$_POST["color"]."', ";
$SQL .="  altuno='".$_POST["altuno"]."', ";
$SQL .="  altdos='".$_POST["altdos"]."', ";
$SQL .="  interno='".$_POST["interno"]."', ";
$SQL .="  nroorden='".$_POST["nroorden"]."', ";
$SQL .="  aniou='".$_POST["aniousa"]."', ";
$SQL .="  coloru='".$_POST["colorusa"]."', ";
$SQL .="  internou='".$_POST["internou"]."', ";
$SQL .="  detalleu='".$_POST["detalleu"]."', ";
$SQL .="  dominiou='".$_POST["dominiou"]."', ";
$SQL .="  tipocompra='".$_POST["tipocompra"]."', ";
$SQL .="  marcareem='".$_POST["marcareem"]."', ";
$SQL .="  modeloreem='".$_POST["modeloreem"]."', ";
$SQL .="  anioreem='".$_POST["anioreem"]."', ";
// $SQL .="  confort='".$_POST["confort"]."', ";
// $SQL .="  disenio='".$_POST["disenio"]."', ";
// $SQL .="  equipamiento='".$_POST["equipamiento"]."', ";
// $SQL .="  garantia='".$_POST["garantia"]."', ";
// $SQL .="  marcatoyota='".$_POST["marcatoyota"]."', ";
// $SQL .="  precio='".$_POST["precio"]."', ";
$SQL .="  porque_no='".$_POST["porque_no"]."', ";
// $SQL .="  otra='".$_POST["otra"]."', ";
$SQL .="  venta='".$_POST["venta"]."', ";
$SQL .="  ofreciotd='".$_POST["ofreciotd"]."', ";
$SQL .="  realizotd='".$_POST["realizotd"]."', ";
$SQL .="  enviada=".$_POST["enviado"].", ";

$SQL .="  modificaciones =' ".cambiarFormatoFecha(date("Y-m-d"))." ".$_POST["obs_cambio"]."  *   ||   *  ".$_POST["obs_cambio_a"]."', ";

	if ($_POST["obs_cambio_a"]!="" OR $_POST["obs_cambio_a"]!=null OR $_POST["obs_cambio"]!="") { // si la observaciones anteriores esta vacia entonces es una reserva nueva o sin modificaciones previas
	$SQL .="  modificaciones =' ".cambiarFormatoFecha(date("Y-m-d"))." ".$_POST["obs_cambio"]."  *   ||   *  ".$_POST["obs_cambio_a"]."', ";
	};
$SQL .="  modalt ='".$_POST["modalt"]."' ";
$SQL .=" WHERE idreserva =".$_POST["nrores"];
mysqli_query($con, $SQL);

$SQL="SELECT * FROM grupos WHERE idgrupo=".$_POST["grupo"];
$gru=mysqli_query($con, $SQL);
if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

$SQL="SELECT * FROM modelos WHERE idmodelo=".$_POST["modelo"];
$mod=mysqli_query($con, $SQL);
if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}

$email = "online@dyv-online.com.ar";
$mensaje = "Se realizo modificaci&oacute;n de la Reserva";
$parauno = "ventas@derkayvargas.com.ar, arieljergus@derkayvargas.com.ar, lauraderka@derkayvargas.com.ar";
$parados = "rukyguerra@derkayvargas.com.ar, federicorescala@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar";
$para = "arielgutierrez@derkayvargas.com.ar, roquegomez@derkayvargas.com.ar";

$headers = 'From: online@dyv-online.com.ar' . "\r\n" .
    		'Reply-To: online@dyv-online.com.ar' . "\r\n" .
    		'X-Mailer: PHP/' . phpversion();


if ($_POST["enviado"] == 1 ) {

	$titulo="NUEVA RESERVA - ".$_POST["nombre"]." (".$_POST["usuario_a"].")";

	$mensaje = "SE HA REALIZADO LA SIGUIENTE OPERACION\n";
	$mensaje .= "Nro Reserva: ".$_POST["nrores"]."\n";
	$mensaje .= "UNIDAD: ".$_POST["compra"]."\n";
	$mensaje .= "ASESOR: ".$_POST["usuario_a"]."\n";
	$mensaje .= "CLIENTE: ".$_POST["nombre"]."\n";
	$mensaje .= "UNIDAD: ".$grupo["grupo"]." - ".$modelo['modelo']." ".$_POST["detalleu"]."\n";
	$mensaje .= "ENTREGA: ".$_POST["mesentrega"]." - ".$_POST["anoentrega"]."\n";
	$mensaje .= "Link: http://dyvsa.com.ar/web/reserva_web.php?IDrecord=".$_POST["nrores"];

	// Carga de las notificaciones
	$hora=date( 'H:i');

	$SQL="SELECT * FROM notificacionespara WHERE tiponot=1";
	$res=mysqli_query($con, $SQL);

	while ($not=mysqli_fetch_array($res)) {

	$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, interno, modelo, cliente, asesor, visto, obs )";
	$SQL .=" VALUES (1,'".date("Y-m-d")."','$hora','".$not["idusuario"]."','".$_POST["compra"]."','".$_POST["nrores"]."','".$_POST["interno"].$_POST["internou"]."','".$grupo['grupo'].' '.$modelo['modelo']."".$_POST["detalleu"]."','".$_POST["nombre"]."','".$_POST["asesor_res"]."',0,'".$_POST["observacion"]."')";
	mysqli_query($con, $SQL);

	};

	// Carga de las notificaciones

	if (($_POST["compra"]=="Usado") || (($_POST["marcau"]!="") && ($_POST["tipou"]!="") && ($_POST["modelou"]!=""))) {
		//mail($para, $titulo, $mensaje, $headers);
	};

	//mail($parauno, $titulo, $mensaje, $headers);

	//mail($parados, $titulo, $mensaje, $headers);

};

if ($_POST["enviado"] > 1) {

		// Carga de las notificaciones
	$hora=date( 'H:i');

	$SQL="SELECT usuarios.nombre as idusuario FROM reservas Inner Join usuarios ON reservas.idusuario = usuarios.idusuario WHERE reservas.idreserva = ".$_POST["nrores"];
	$idusures= mysqli_query($con, $SQL);
	$idudres=mysqli_fetch_array($idusures);

	$SQL="SELECT * FROM notificacionespara WHERE tiponot=2";
	$res=mysqli_query($con, $SQL);

	while ($not=mysqli_fetch_array($res)) {

	$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, interno, modelo, cliente, asesor, visto, obs )";
	$SQL .=" VALUES (2,'".date("Y-m-d")."','$hora','".$not["idusuario"]."','".$_POST["compra"]."','".$_POST["nrores"]."','".$_POST["interno"].$_POST["internou"]."','".$grupo['grupo'].' '.$modelo['modelo']."".$_POST["detalleu"]."','".$_POST["nombre"]."','".$idudres["idusuario"]."',0,'".$_POST["obs_cambio"]."-".$_POST["observacion"]."')";
	mysqli_query($con, $SQL);

	};

	// Carga de las notificaciones

	$titulo="MODIFICACION DE RESERVA - ".$_POST["nombre"]." (".$_POST["usuario_a"].")";

	$mensaje = "SE HA REALIZADO MODIFICACIONES EN LA SIGUIENTE RESERVA";
	$mensaje .=" Nro Reserva: ".$_POST["nrores"]."\n";
	$mensaje .= "UNIDAD: ".$_POST["compra"]."\n";
	$mensaje .= "ASESOR: ".$_POST["usuario_a"]."\n";
	$mensaje .= "CLIENTE: ".$_POST["nombre"]."\n";
	$mensaje .= "UNIDAD: ".$grupo["grupo"]." - ".$modelo['modelo']." ".$_POST["detalleu"]."\n";
	$mensaje .= "ENTREGA: ".$_POST["mesentrega"]." - ".$_POST["anoentrega"]."\n";
	$mensaje .= "OBSERVACION: ".$_POST["obs_cambio"]."\n";
	$mensaje .= "LINK: http://dyvsa.com.ar/web/reserva_web.php?IDrecord=".$_POST["nrores"];


// if (($_POST["compra"]=="Usado") || (($_POST["marcau"]!="") && ($_POST["tipou"]!="") && ($_POST["modelou"]!=""))) {
// 			mail($para, $titulo, $mensaje, $headers);
// 	};

	//mail($parauno, $titulo, $mensaje, $headers);

	//mail($parados, $titulo, $mensaje, $headers);

 };


 mysqli_close($con);

 @session_start();

 if ($_SESSION["id"]==11) {
 	header("Location: control_pagos_clientes.php");
 }else{
 	header("Location: asesores.php");
 }
 ?>