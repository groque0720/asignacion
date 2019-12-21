<?php

include('../z_comun/vista.php');

@session_start();

extract($_GET);

$id_evaluacion = $id;

if ($_SESSION['acceso_total']!=1) {
	include('resultado-empleado.php');
 }else{
  include('resultado-analisis.php');
 } ?>