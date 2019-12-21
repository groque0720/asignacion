<?php
 include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE facturas SET";
$SQL .=" anombre ='".$_POST["tipo_fact"]."', ";
$SQL .=" nombre ='".$_POST["nombre_cli"].$_POST["nombre_a"]."', ";

if ($_POST["fecnac"]!='') {
	$SQL.=" fecnac = '".$_POST["fecnac"]."', ";
}else{
	$SQL.=" fecnac = null ,";
}
//$SQL .=" fecnac ='".$_POST["fecnac"]."', ";
$SQL .=" tipodoc ='".$_POST["tipodoc"]."', ";
$SQL .=" nrodoc ='".$_POST["nrodoc"]."', ";
$SQL .=" cuil ='".$_POST["cuil"]."', ";
$SQL .=" direccion ='".$_POST["direccion"]."', ";
$SQL .=" localidad ='".$_POST["localidad"]."', ";
$SQL .=" provincia ='".$_POST["provincia"]."', ";
$SQL .=" mail ='".$_POST["mail"]."', ";
$SQL .=" tfijo ='".$_POST["tfijo"]."', ";
$SQL .=" tcelu ='".$_POST["tcelu"]."', ";
$SQL .=" estado =".$_POST["estado"].", ";

if ($_POST["fecped"]!='') {
	$SQL.=" fecped = '".$_POST["fecped"]."', ";
}else{
	$SQL.=" fecped = null ,";
}

//$SQL .=" fecped ='".$_POST["fecped"]."', ";
$SQL .=" observacion ='".$_POST["observacion"]."' ";
$SQL .=" WHERE idfactura =".$_POST["idfact"];
mysqli_query($con, $SQL);

if ($_POST["estado"] != 0) {


	$SQL="SELECT * FROM clientes WHERE idcliente =".$_POST["idcliente"];
	$res=mysqli_query($con, $SQL);
	$clientes=mysqli_fetch_array($res);

	// Carga de las notificaciones

	$SQL="SELECT * FROM reservas WHERE idreserva =".$_POST["nrores"];
	$reg_res=mysqli_query($con, $SQL);
	$res=mysqli_fetch_array($reg_res);

	$SQL="SELECT * FROM grupos WHERE idgrupo=".$res["idgrupo"];
	$gru=mysqli_query($con, $SQL);
	if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

	$SQL="SELECT * FROM modelos WHERE idmodelo=".$res["idmodelo"];
	$mod=mysqli_query($con, $SQL);
	if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}

	$hora=date( 'H:i');
	$SQL="SELECT * FROM notificacionespara WHERE tiponot=4";
	$resul=mysqli_query($con, $SQL);

	while ($not=mysqli_fetch_array($resul)) {

	$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, idfactura, compra, idreserva, interno, modelo, cliente, asesor, visto, obs )";
	$SQL .=" VALUES (4,'".date("Y-m-d")."','$hora','".$not["idusuario"]."','".$_POST["idfact"]."','".$res["compra"]."','".$_POST["nrores"]."','".$res["interno"].$res["internou"]."','".$grupo['grupo']." ".$modelo['modelo']."".$res["detalleu"]."','".$_POST["nombre_a"]." - ".$clientes["nombre"]."','".$_POST["asesor_vta"]."',0,'".$_POST["observacion"]."')";
	mysqli_query($con, $SQL);
	}

}
 mysqli_close($con);
header("Location: asesores.php");
 ?>