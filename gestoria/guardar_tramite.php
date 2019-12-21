<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);


$SQL="UPDATE registros_gestoria_clientes SET guardado = 1 WHERE id_reg_gestoria =".$_POST['id_reg_gestoria'];
mysqli_query($con, $SQL);

$SQL ="SELECT id_cliente_gestoria FROM registros_gestoria_clientes WHERE id_reg_gestoria =".$_POST['id_reg_gestoria'];
$res_cant = mysqli_query($con, $SQL);
$cant = mysqli_num_rows($res_cant);


$SQL="UPDATE registros_gestoria SET ";
// $SQL.=" nro_rva = ".$_POST["nro_rva"].",";
$SQL.=" fec_rec_tra = '".$_POST["fec_rec_tra"]."',";
$SQL.=" id_asesor = ".$_POST["asesor"].", ";
$SQL.=" compra = ".$_POST["compra"].",";
$SQL.=" interno = '".$_POST["interno"]."',";
$SQL.=" id_modelo = ".$_POST["modelo"].",";
$SQL.=" id_version = ".$_POST["version"].", ";
$SQL.=" usado = '".$_POST["usado"]."',";
$SQL.=" prenda = ".$_POST["credito"].",";
$SQL.=" financiera='".$_POST["financiera"]."',"; 
$SQL.=" tipo_persona = ".$_POST["tipo_persona"].",";
$SQL.=" estado_reg = ".$_POST["estado_reg"].",";
$SQL.=" cant_miembro = ".$cant.", ";
$SQL.=" patente = '".$_POST["patente"]."', ";
$SQL.=" id_gestor = ".$_POST["gestor"].", ";
$SQL.=" id_provincia = ".$_POST["provincia"].",";
$SQL.=" id_localidad = ".$_POST["loc_registro"].",";

if ($_POST["fec_rec_gestoria"]!='') {
	$SQL.=" fec_rec_gestoria = '".$_POST["fec_rec_gestoria"]."', ";
}else{
	$SQL.=" fec_rec_gestoria = null ,";
}

if ($_POST["fec_ins"]!='') {
	$SQL.=" fec_ins = '".$_POST["fec_ins"]."', ";
}else{
	$SQL.=" fec_ins = null ,";
}
 
$SQL.=" notas = '".$_POST["notas"]."' ";
$SQL.=" WHERE id_reg_gestoria =".$_POST['id_reg_gestoria'];
mysqli_query($con, $SQL);



// header("Location: tramite.php?id=".$_POST['id_reg_gestoria']);
header("Location: index.php");
//fec_ins = '$fec_ins',

?>
