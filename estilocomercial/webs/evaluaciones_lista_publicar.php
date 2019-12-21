<?php

	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");

	extract($_POST);

	$SQL="UPDATE evaluaciones_realizadas SET publicado = 1 WHERE id_evaluacion_realizada =".$id_eva;
	mysqli_query($con, $SQL);


	$con = mysql_connect('localhost','root','872') or die("ERROR EN CONEXION:".mysql_error());
	$base_datos=mysql_select_db('dyv', $con) or die("ERROR AL SELECCIONAR LA BASE DE DATOS:".mysql_error());
	mysql_query("SET NAMES 'utf8'");




   $id_tema=7;
   $obs="Evaluación por Objetivos";
   $url_objetivo = "../../estilocomercial/webs/".$url_objetivo;
   $url_factor = "../../estilocomercial/webs/".$url_factor;
   $SQL="SELECT * FROM usuarios WHERE id_usuario_dos = ".$idusu;
   $res_usu=mysqli_query($con, $SQL);
   $usu_reg=mysqli_fetch_array($res_usu);
   $idasesor = $usu_reg['idusuario'];
   $idsucursal = $usu_reg['idsucursal'];
   $fecha=date('Y-m-d');

	$SQL="INSERT INTO publicaciones (fecha, idsucursal, idusuario, id_tema, obs, url)VALUES('$fecha','$idsucursal','$idasesor','$id_tema','$obs','$url_objetivo')";
	mysqli_query($con, $SQL);

	// include("publicaciones_lista_cuerpo.php");

	$rs = mysql_query("SELECT MAX(id_publicacion) AS id FROM publicaciones");
	if ($row = mysql_fetch_row($rs)) {
	$id_publicacion= trim($row[0]);
	}

	$SQL="INSERT INTO publicaciones_linea (id_publicacion, idusuario, id_tema, url) VALUES ('$id_publicacion', '$idasesor','$id_tema', '$url_objetivo')";
	mysqli_query($con, $SQL);


	$obs="Evaluación por Factores";

	$SQL="INSERT INTO publicaciones (fecha, idsucursal, idusuario, id_tema, obs, url)VALUES('$fecha','$idsucursal','$idasesor','$id_tema','$obs','$url_factor')";
	mysqli_query($con, $SQL);

	// include("publicaciones_lista_cuerpo.php");

	$rs = mysql_query("SELECT MAX(id_publicacion) AS id FROM publicaciones");
	if ($row = mysql_fetch_row($rs)) {
	$id_publicacion= trim($row[0]);
	}

	$SQL="INSERT INTO publicaciones_linea (id_publicacion, idusuario, id_tema, url) VALUES ('$id_publicacion', '$idasesor','$id_tema', '$url_factor')";
	mysqli_query($con, $SQL);


 ?>