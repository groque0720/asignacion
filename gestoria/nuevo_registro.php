<?php 
@session_start();
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
$id_suc=$_SESSION["idsuc"]; 

// $id_suc=1; 

$SQL="SELECT sucres FROM sucursales WHERE idsucursal =".$id_suc;
$res_suc=mysqli_query($con, $SQL);
$suc=mysqli_fetch_array($res_suc);


// $SQL="SELECT max(leg) as max_num FROM registros_gestoria WHERE guardado=1 AND id_sucursal = ".$id_suc;
// $bus_nro=mysqli_query($con, $SQL);
// if (empty($bus_nro)) {$prox_nro=1;}else{ $prox_nro_a=mysqli_fetch_array($bus_nro); $prox_nro=$prox_nro_a['max_num'];}

// if (is_null($prox_nro)) {
// 	$prox_nro = 1;
// }else{
// 	$prox_nro ++;
// }

$SQL = "INSERT INTO registros_gestoria (id_sucursal, estado_reg) VALUES ('".$id_suc."', 0)";
mysqli_query($con, $SQL);


//busco su id, del registro recien creado
$SQL="SELECT max(id_reg_gestoria) as id FROM registros_gestoria LIMIT 1";
$res_reg = mysqli_query($con, $SQL);
$nro_leg = mysqli_num_rows($res_reg);

$max_nro_leg = mysqli_fetch_array($res_reg);


// creo con un cliente con el id de registro recien creado
$SQL = "INSERT INTO registros_gestoria_clientes (id_reg_gestoria, guardado) VALUES ('".$max_nro_leg['id']."', 1) ";
mysqli_query($con, $SQL);

$SQL="SELECT max(id_cliente_gestoria) as id FROM registros_gestoria_clientes LIMIT 1";
$res_cli= mysqli_query($con, $SQL);
$max_nro_cli = mysqli_fetch_array($res_cli);
$id_cli=$max_nro_cli['id'];

// preparo la documentacion del registro y clientes 
$SQL="SELECT * FROM registros_gestoria_uif_doc WHERE activo = 1";
$res_doc = mysqli_query($con, $SQL);

while ($doc = mysqli_fetch_array($res_doc)) {
	$SQL = "INSERT INTO registros_gestoria_clientes_doc (id_cliente_gestoria, id_doc_uif) VALUES (".$id_cli.",".$doc['id_doc_uif'].")";
	mysqli_query($con, $SQL);
}


header("Location: tramite.php?id=".$max_nro_leg['id']);



 ?>