<!DOCTYPE html>
<html lang="es">
<head>
    <title>Panel de Aplicaciones</title>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="500">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="imagenes/favicon.ico">

    <style>
        /* ======================
           RESET BÁSICO
        ====================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ======================
           HEADER
        ====================== */
        .header {
            max-width: 1100px;
            margin: 20px auto;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .header img {
            max-width: 90px;
            height: auto;
        }

        .header h1 {
            font-size: 1.2rem;
            text-align: center;
            flex: 1;
        }

        .divider {
            max-width: 1100px;
            margin: 10px auto 30px;
            border-bottom: 1px solid #ddd;
        }

        /* ======================
           GRID DE APLICACIONES
        ====================== */
        .apps-container {
            max-width: 1100px;
            margin: auto;
            padding: 0 15px 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
        }

        /* ======================
           CARD
        ====================== */
        .item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px 10px;
            text-align: center;
            position: relative;
            transition: all 0.25s ease;
        }

        .item:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0,0,0,.12);
        }

        .item:hover .titulo {
            color: #d40000;
        }

        .item img {
            width: 70px;
            height: auto;
            margin-bottom: 10px;
        }

        .titulo {
            font-size: 0.9rem;
            font-weight: 600;
        }

        /* ======================
           BADGE NOTIFICACIÓN
        ====================== */
        .cantidad {
            position: absolute;
            top: -6px;
            right: -6px;
            background: red;
            color: white;
            font-size: 0.75rem;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 50%;
        }

        /* ======================
           RESPONSIVE AJUSTES
        ====================== */
        @media (max-width: 600px) {
            .header h1 {
                font-size: 1rem;
            }

            .item img {
                width: 60px;
            }
        }
    </style>
</head>

<body>

<?php
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
@session_start();

if ($_SESSION["autentificado"] != "SI") {
    header("Location: ../login");
    exit();
}
?>

<!-- ================= HEADER ================= -->
<header class="header">
    <img src="imagenes/logodyv.png" alt="DYV">
    <h1>Panel de Aplicaciones</h1>
    <img src="imagenes/logo_toyota.png" alt="Toyota">
</header>

<div class="divider"></div>

<!-- ================= APLICACIONES ================= -->
<section class="apps-container">

<?php
$SQL = "
SELECT
    aplicaciones.id_aplicacion,
    aplicaciones.url,
    aplicaciones.imagen,
    aplicaciones.aplicacion
FROM usuarios_aplicaciones
JOIN aplicaciones ON usuarios_aplicaciones.id_aplicaciones = aplicaciones.id_aplicacion
JOIN usuarios ON usuarios_aplicaciones.id_usuario = usuarios.idusuario
WHERE aplicaciones.activo = 1
AND usuarios_aplicaciones.id_usuario = ".$_SESSION['id']."
ORDER BY aplicaciones.aplicacion
";

$aplicaciones = mysqli_query($con, $SQL);

while ($app = mysqli_fetch_array($aplicaciones)) {
?>

    <div class="item">
        <?php
        if ($app['id_aplicacion'] == 17) {
            $SQL = "SELECT COUNT(*) AS cantidad FROM recepcion WHERE visto = 0 AND id_asesor = ".$_SESSION["id"];
            $res = mysqli_query($con, $SQL);
            $noti = mysqli_fetch_array($res);
            if ($noti['cantidad'] > 0) {
                echo '<div class="cantidad">'.$noti['cantidad'].'</div>';
            }
        }
        ?>

        <a target="_blank" href="<?= $app['url'] ?>">
            <img src="<?= $app['imagen'] ?>" alt="<?= $app['aplicacion'] ?>">
            <div class="titulo"><?= $app['aplicacion'] ?></div>
        </a>
    </div>

<?php } ?>

<?php if ($_SESSION["idperfil"] == 3): ?>
    <div class="item">
        <a target="_blank" href="https://cms.derkayvargas.com/infoauto">
            <img src="https://i.pinimg.com/280x280_RS/2a/f9/3e/2af93ee5076e04395b5e9c8657adff7c.jpg">
            <div class="titulo">InfoAuto</div>
        </a>
    </div>

    <?php if($_SESSION["id_negocio"] == 2 && $_SESSION["es_gerente"] == 0): ?>
        <div class="item">
            <a href="/planes_avanzados/">
                <img src="/dashboard/imagenes/logo-tpa.png">
                <div class="titulo">Avanzados y Adjudicados DYV</div>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>

</section>

</body>
</html>
