<?php
    include("funciones/func_mysql.php");
    conectar();
    @session_start();
    //COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
    if ($_SESSION["autentificado"] != "SI") {
        //si no existe, envio a la página de autentificacion
        header("Location: ../login");
        //ademas salgo de este script
        exit();
    }

    // print_r($_SESSION);
    $userId = $_SESSION["id"];
    $userName = $_SESSION["usuario"];
    $basePath = rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/');