<?php
session_start();

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="INSERT INTO clientes(activo) VALUES (1)";
mysqli_query($con, $SQL);

$rs = mysql_query("SELECT MAX(idcliente) AS id FROM clientes");
if ($row = mysql_fetch_row($rs)) {
$idcliente = trim($row[0]);
}

$SQL="INSERT INTO entregausado(marca) VALUES ('.')";
mysqli_query($con, $SQL);

$rs = mysql_query("SELECT MAX(identregau) AS id FROM entregausado");
if ($row = mysql_fetch_row($rs)) {
$identregau = trim($row[0]);
}

$SQL="INSERT INTO facturas(anombre, estado) VALUES ('propio', 0)";
mysqli_query($con, $SQL);

$rs = mysql_query("SELECT MAX(idfactura) AS id FROM facturas");
if ($row = mysql_fetch_row($rs)) {
$idfactura = trim($row[0]);
}

$SQL="INSERT INTO facturas(anombre, estado) VALUES ('propio', 0)";
mysqli_query($con, $SQL);

$rs = mysql_query("SELECT MAX(idfactura) AS id FROM facturas");
if ($row = mysql_fetch_row($rs)) {
$idfactura = trim($row[0]);
}

$SQL="INSERT INTO creditos(estado, activo) VALUES (0, 1)";
mysqli_query($con, $SQL);

$rs = mysql_query("SELECT MAX(idcredito) AS id FROM creditos");
if ($row = mysql_fetch_row($rs)) {
$idcredito = trim($row[0]);
}

$SQL="INSERT INTO reservas(idusuario, fecres, idcliente, identregau, idfactura, idcredito, enviada, anulada, entregada, cancelada, entregadoc, marca)";
$SQL .=" VALUES (".$_SESSION["id"].", '".date("Y-m-d")."', ".$idcliente.",".$identregau.",".$idfactura.",".$idcredito.",0,0,0,0,0, 'Toyota')";
mysqli_query($con, $SQL);


$rs = mysql_query("SELECT MAX(idreserva) AS id FROM reservas");
if ($row = mysql_fetch_row($rs)) {
$idreserva = trim($row[0]);
}


$SQL="SELECT * FROM lineas_detalle WHERE idreserva='".$_POST["nrores"]."'";
$res=mysqli_query($con, $SQL);

while ($not=mysqli_fetch_array($res)) {

// $SQL="INSERT INTO lineas_detalle(monto,movimiento,moneda,credito)";
// $SQL .=" VALUES ('".$idreserva."','".$not["idcodigo"]."','".$not["detalle"]."','".$not["adjunto"]."','".$not["monto"]."','".$not["movimiento"]."','".$not["credito"]."'')";

// idcodigo, codigo, idreserva, detalle, adjunto, movimiento, monto, credito

$SQL="INSERT INTO lineas_detalle(idreserva, idcodigo, detalle, adjunto, monto, movimiento,  credito)";
$SQL .=" VALUES (".$idreserva.",".$not["idcodigo"].",'".$not["detalle"]."','".$not["adjunto"]."',".$not["monto"].",'".$not["movimiento"]."','".$not["credito"]."')";
mysqli_query($con, $SQL);

}


$SQL="INSERT INTO pagos(idreserva,cancelado) VALUES(".$idreserva.", 0)";
mysqli_query($con, $SQL);

$SQL = "UPDATE entregausado SET";
$SQL .=" marca ='".$_POST["marcau"]."', ";
$SQL .=" tipo ='".$_POST["tipou"]."', ";
$SQL .=" modelo ='".$_POST["modelou"]."', ";
$SQL .=" color ='".$_POST["coloru"]."', ";
$SQL .=" anio ='".$_POST["aniou"]."', ";
$SQL .=" dominio ='".$_POST["dominio"]."', ";
$SQL .=" km ='".$_POST["km"]."', ";
$SQL .=" info ='".$_POST["info"]."' ";
$SQL .=" WHERE identregau =".$identregau;
mysqli_query($con, $SQL);


$SQL = "UPDATE clientes SET";
$SQL .=" nombre ='".$_POST["nombre"]."', ";
$SQL .=" sexo ='".$_POST["sexo"]."', ";
$SQL .=" fecnac ='".$_POST["fecnac"]."', ";
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
$SQL .=" prefcontacto='".$_POST["prefcontacto"]."', ";
$SQL .=" mail ='".$_POST["mail"]."', ";
$SQL .=" tfijo ='".$_POST["tfijo"]."', ";
$SQL .=" tcelu ='".$_POST["tcelu"]."', ";
$SQL .=" ctoyota ='".$_POST["ctoyota"]."'";
$SQL .=" WHERE idcliente =".$idcliente;
mysqli_query($con, $SQL);



$SQL="UPDATE reservas SET";
$SQL .="  observacion ='".$_POST["observacion"]."', ";
$SQL .="  compra='".$_POST["compra"]."', ";
$SQL .="  hora='".date("H:i:s")."', ";
$SQL .="  nrounidad='-', ";
$SQL .="  fecres='".date("Y-m-d")."', ";
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
$SQL .="  confort='".$_POST["confort"]."', ";
$SQL .="  disenio='".$_POST["disenio"]."', ";
$SQL .="  equipamiento='".$_POST["equipamiento"]."', ";
$SQL .="  garantia='".$_POST["garantia"]."', ";
$SQL .="  marcatoyota='".$_POST["marcatoyota"]."', ";
$SQL .="  precio='".$_POST["precio"]."', ";
$SQL .="  otra='".$_POST["otra"]."', ";
$SQL .="  venta='".$_POST["venta"]."', ";
$SQL .="  enviada=0, ";
	if ($_POST["obs_cambio_a"]!="" OR $_POST["obs_cambio_a"]!=null OR $_POST["obs_cambio"]!="") { // si la observaciones anteriores esta vacia entonces es una reserva nueva o sin modificaciones previas
	$SQL .="  modificaciones =' ".cambiarFormatoFecha(date("Y-m-d"))." ".$_POST["obs_cambio"]."  *   ||   *  ".$_POST["obs_cambio_a"]."', ";
	};
$SQL .="  modalt ='".$_POST["modalt"]."' ";
$SQL .=" WHERE idreserva =".$idreserva;
mysqli_query($con, $SQL);

mysqli_close($con);
header("Location: reserva_clonar.php?IDrecord=$idreserva");

 ?>