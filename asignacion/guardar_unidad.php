<?php

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

@session_start();
$id_perfil=$_SESSION["idperfil"];
$id_sucursal=$_SESSION["idsuc"];
$id_usuario = $_SESSION["id"];
$nom_asesor=$_SESSION["usuario"];

// $autorizados = []


$sql="SELECT estado_reserva, hora, reservada FROM asignaciones  WHERE id_unidad =".$_POST['id_unidad'];
$unidades = mysqli_query($con, $sql);
$unidad = mysqli_fetch_array($unidades);
$estaba_pisada = false;

if ($_POST['estado_reserva']==0 AND $_POST['reservada']==0 AND $unidad["reservada"]==1) {
	echo "<script>swal('Reserva ya pisada!', 'La unidad ha sido pisada por otro asesor antes que ti', 'error');</script>";
	$estaba_pisada = true;
}

if ($estaba_pisada==false) {

	$SQL="UPDATE asignaciones SET ";

	if ($_POST["nro_unidad"]!=0 AND $_POST["nro_unidad"]!='' AND $_POST["nro_unidad"]!=null) {
		$SQL.=" nro_unidad = ".$_POST["nro_unidad"].",";
	}else{
		$SQL.=" nro_unidad = null,";
	}

	$SQL.=" id_negocio = ".$_POST["id_negocio"].",";
	$SQL.=" id_mes = ".$_POST["id_mes"].",";
	$SQL.=" año = ".$_POST["año"].",";
	$SQL.=" id_grupo = ".$_POST["id_grupo"].",";
	$SQL.=" id_modelo = ".$_POST["id_modelo"].",";
	$SQL.=" nro_orden = '".trim($_POST["nro_orden"])."',";
	$SQL.=" interno = '".trim($_POST["interno"])."',";

	if ($_POST["id_color"]!='') {
		$SQL.=" id_color = ".$_POST["id_color"].", ";
	}else{
		$SQL.=" id_color = 0 ,";
	}

	$SQL.=" chasis = '".trim($_POST["chasis"])."',";
	$SQL.=" id_sucursal = ".$_POST["id_sucursal"].",";
	$SQL.=" id_ubicacion = ".$_POST["id_ubicacion"].",";
	$SQL.=" estado_tasa = ".$_POST["estado_tasa"].",";
	$SQL.=" hora = '".$_POST["hora"]."',";


	if ($_POST["fec_despacho"]!='') {
		$SQL.=" fec_despacho = '".$_POST["fec_despacho"]."', ";
	}else{
		$SQL.=" fec_despacho = null ,";
	}

	if ($_POST["fec_arribo"]!='') {
		$SQL.=" fec_arribo = '".$_POST["fec_arribo"]."', ";
	}else{
		$SQL.=" fec_arribo = null ,";
	}
	if (isset($_POST["fec_entrega"])) {

		if ($_POST["fec_entrega"]!='') {
			$SQL.=" fec_entrega = '".$_POST["fec_entrega"]."', ";
			$SQL.=" entregada = 1 , ";
		}else{
			$SQL.=" fec_entrega = null ,";
			$SQL.=" entregada = 0 , ";
		}
	}

	if (isset($_POST["nro_remito"])) {
	$SQL.=" nro_remito = '".$_POST["nro_remito"]."', ";
	}


	if ($_POST["fec_playa"]!='') {
		$SQL.=" fec_playa = '".$_POST["fec_playa"]."', ";
	}else{
		$SQL.=" fec_playa = null ,";
	}

	$SQL.=" costo = ".$_POST["costo"].",";

		//if ($_POST["pagado"]!=true) {
			//$SQL.=" pagado = 0, ";
		//}else{
			//$SQL.=" pagado = 1, ";
		//}

	if ($_POST["interno"]!='' AND $_POST["interno"]!=null) {
		$SQL.=" pagado = 1, ";

	}else{
		$SQL.=" pagado = 0, ";
	}


	$SQL.=" estado_reserva = ".$_POST["estado_reserva"].",";


	if ($_POST["fec_reserva"]!='') {
		$SQL.=" fec_reserva = '".$_POST["fec_reserva"]."', ";
		$SQL.=" reservada = 1, ";
	}else{
		$SQL.=" fec_reserva = null ,";
		$SQL.=" reservada = 0, ";
	}

	$SQL.=" cliente = '".trim(trim($_POST["cliente"]),'-')."',";
	$SQL.=" id_asesor = '".$_POST["id_asesor"]."',";
	$SQL.=" color_uno = ".$_POST["color_uno"].",";
	$SQL.=" color_dos = ".$_POST["color_dos"].",";
	$SQL.=" color_tres = ".$_POST["color_tres"].",";


	if ($_POST["fec_limite"]!='') {
		$SQL.=" fec_limite = '".$_POST["fec_limite"]."', ";
	}else{
		$SQL.=" fec_limite = null ,";
	}

	if ($_POST["fec_inscripcion"]!='') {
		$SQL.=" fec_inscripcion = '".$_POST["fec_inscripcion"]."', ";
	}else{
		$SQL.=" fec_inscripcion = null ,";
	}

	if ($_POST["fec_cancelacion"]!='') {
		$SQL.=" fec_cancelacion = '".$_POST["fec_cancelacion"]."', ";
	}else{
		$SQL.=" fec_cancelacion = null ,";
	}

	if ($_POST["fec_cancelacion"]!='' AND $_POST["fec_cancelacion"]!=null) {
		$SQL.=" cancelada = 1, ";
	}else{
		$SQL.=" cancelada = 0, ";
	}

	$SQL.=" patente = '".$_POST["patente"]."',";

	if ($_POST["fec_inscripcion"]!='' AND $_POST["fec_inscripcion"]!=null) {
		$SQL.=" patentada = 1, ";
	}else{
		$SQL.=" patentada = 0, ";
	}

	if ($_POST["fec_pedido"]!='') {
		$SQL.=" fec_pedido = '".$_POST["fec_pedido"]."', ";
	}else{
		$SQL.=" fec_pedido = null ,";
	}

	if ($_POST["hora_pedido"]!='') {
		$SQL.=" hora_pedido = '".$_POST["hora_pedido"]."', ";
	}else{
		$SQL.=" hora_pedido = null ,";
	}

		if ($_POST["no_disponible"]==true) {
			$SQL.=" no_disponible = 0, ";
		}else{
			$SQL.=" no_disponible = 1, ";
		}

		if ( isset($_POST["reventa"]) AND $_POST["reventa"]==true) {
			$SQL.=" reventa = 1, ";
		}else{
			$SQL.=" reventa = 0, ";
		}



	// $SQL.=" hora_pedido = '".$_POST["hora_pedido"]."',";

	$SQL.=" id_estado_entrega = ".$_POST["id_estado_entrega"].",";
	$SQL.=" id_ubicacion_entrega = ".$_POST["id_ubicacion_entrega"].",";

	$SQL.=" observacion = '".$_POST["observacion"]."',";
	$SQL.=" guardado = 1 ";
	$SQL.=" WHERE id_unidad =".$_POST['id_unidad'];
	mysqli_query($con, $SQL);

	$modelo_activo = $id_modelo;

	$SQL="INSERT INTO a_modificaciones (modelo_activo, fecha) VALUES($modelo_activo,'".date("Y-m-d")."')";
	mysqli_query($con, $SQL);

} //en fin de if que consulta si ya estaba pisada la unidad

$modelo_activo = $id_modelo;

if ($_POST['es_planilla_tpa']!='es_tpa') {

	if ($_POST['id_perfil']==5 ) { // si es planilla entrega

		if ($_POST['es_planilla_entregas']!='es_entrega') { // si es distinto de entregas cargar la planilla asignación

			if ($text_busqueda!='' AND $text_busqueda!=null ) {
				$abuscar=$text_busqueda;
				include ('busqueda_rapida_unidades_cuerpo.php');
			}else{
				include ('contenido_relleno.php');
			}

		}else{ // si es igual a planilla entregas cargo contenido de la planilla de entregas

			if ($text_busqueda!='' AND $text_busqueda!=null ) {
				$abuscar=$text_busqueda;
				include ('entregas_busqueda_rapida_unidades_cuerpo.php');
			}else{
				include('entregas_contenido_relleno.php');
			}

		}


	}else{ // cargo la planilla de asignacion


		if ($text_busqueda!='' AND $text_busqueda!=null ) {
			$abuscar=$text_busqueda;
			include ('busqueda_rapida_unidades_cuerpo.php');
		}else{
			include ('contenido_relleno.php');
		}

	}

}else{

	if ($text_busqueda!='' AND $text_busqueda!=null ) {
		$abuscar=$text_busqueda;
		include ('plan_ahorro_busqueda_rapida_unidades_cuerpo.php');
	}else{
		include ('plan_ahorro_contenido_relleno_total.php');

}
}
 ?>}
