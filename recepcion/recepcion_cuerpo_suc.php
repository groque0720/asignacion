<?php
 
extract($_POST);
@session_start();

$_SESSION["idsuc"]=$id_suc;

include('recepcion_cuerpo.php');

 ?>