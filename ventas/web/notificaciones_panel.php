<!DOCTYPE html>
<html lang="es">
<head>
    <title>Panel de Notificaciones</title>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="500">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* ================= RESET ================= */
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

        /* ================= HEADER ================= */
        .header {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .header img {
            max-width: 90px;
        }

        .header h1 {
            font-size: 1.2rem;
            text-align: center;
            flex: 1;
        }

        .divider {
            max-width: 1200px;
            margin: 10px auto 30px;
            border-bottom: 1px solid #ddd;
        }

        /* ================= GRID ================= */
        .apps-grid {
            max-width: 1200px;
            margin: auto;
            padding: 0 16px 80px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 18px;
        }

        /* ================= CARD ================= */
        .item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 16px 10px;
            text-align: center;
            position: relative;
            transition: all 0.25s ease;
        }

        .item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0,0,0,.12);
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

        /* ================= BADGE ================= */
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

        /* ================= FOOTER INFO ================= */
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            text-align: center;
            padding: 6px 10px;
            font-size: 11px;
            font-style: italic;
        }

        /* ================= RESPONSIVE ================= */
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
include("../includes/security.php");
include("../funciones/func_mysql.php");
conectar();
@session_start();
?>

<!-- ================= HEADER ================= -->
<header class="header">
    <img src="../imagenes/logodyv.png" alt="DYV">
    <h1>Panel de Notificaciones</h1>
    <img src="../imagenes/logo_toyota.png" alt="Toyota">
</header>

<div class="divider"></div>

<!-- ================= GRID ================= -->
<section class="apps-grid">

    <!-- CARD BASE -->
    <div class="item">
        <a href="../../asignacion" target="_blank">
            <img src="../imagenes/asignacion.PNG">
            <div class="titulo">Planilla Asignación</div>
        </a>
    </div>

    <div class="item">
        <a href="../../mistery" target="_blank">
            <img src="../imagenes/mistery.jpg">
            <div class="titulo">Atención Misterys</div>
        </a>
    </div>

    <div class="item">
        <a href="asesores.php" target="_blank">
            <img src="../imagenes/reservas.png">
            <div class="titulo">Operaciones</div>
        </a>
    </div>

    <div class="item">
        <a href="/planes_avanzados/">
            <img src="/dashboard/imagenes/logo-tpa.png">
            <div class="titulo">Avanzados y Adjudicados DYV</div>
        </a>
    </div>

    <div class="item">
        <a href="/uif/Manual-UIF-DyV.pdf" target="_blank">
            <img src="/dashboard/imagenes/uif.PNG">
            <div class="titulo">Manual UIF</div>
        </a>
    </div>

    <div class="item">
        <a href="https://cms.derkayvargas.com/infoauto" target="_blank">
            <img src="https://i.pinimg.com/280x280_RS/2a/f9/3e/2af93ee5076e04395b5e9c8657adff7c.jpg">
            <div class="titulo">InfoAuto</div>
        </a>
    </div>

    <div class="item">
        <a href="http://crm.derkayvargas.com" target="_blank">
            <img src="../imagenes/crm.jpg">
            <div class="titulo">CRM</div>
        </a>
    </div>

    <?php if ($_SESSION["id"] == 94 || $_SESSION["id"] == 96): ?>
        <div class="item">
            <a href="pagos_clientes.php">
                <img src="../imagenes/pagos.png">
                <div class="titulo">Control de Pagos</div>
            </a>
        </div>

        <div class="item">
            <a href="/gestoria">
                <img src="../../dashboard/imagenes/gestoria.png">
                <div class="titulo">Gestoría</div>
            </a>
        </div>
    <?php endif; ?>

    <?php
    $SQL="SELECT count(*) as cantidad FROM notificaciones WHERE idusuario=".$_SESSION["id"]." AND visto=0 AND borrar=0";
    $res=mysqli_query($con, $SQL);
    $cant=mysqli_fetch_array($res);
    ?>
    <div class="item">
        <?php if ($cant['cantidad'] > 0): ?>
            <div class="cantidad"><?= $cant['cantidad'] ?></div>
        <?php endif; ?>
        <a href="noticias.php">
            <img src="../imagenes/notificaciones.png">
            <div class="titulo">Notificaciones Reservas</div>
        </a>
    </div>

    <?php
    $SQL="SELECT * FROM publicaciones_temas WHERE activo = 1";
    $res_temas=mysqli_query($con, $SQL);
    $usu=$_SESSION["id"];

    while ($tema=mysqli_fetch_array($res_temas)):
        $SQL="SELECT count(*) as cantidad FROM publicaciones_linea 
              WHERE id_tema=".$tema['id_publicacion_tema']." 
              AND visto=0 AND idusuario=$usu";
        $r=mysqli_query($con,$SQL);
        $n=mysqli_fetch_array($r);
    ?>
        <div class="item">
            <?php if ($n['cantidad'] > 0): ?>
                <div class="cantidad"><?= $n['cantidad'] ?></div>
            <?php endif; ?>
            <a href="notificaciones_lista_asesores.php?id_tema=<?= $tema['id_publicacion_tema'] ?>&id=<?= $usu ?>">
                <img src="<?= $tema['imagen'] ?>">
                <div class="titulo"><?= $tema['tema'] ?></div>
            </a>
        </div>
    <?php endwhile; ?>

</section>

<!-- ================= FOOTER ================= -->
<div class="footer-note">
    <div>Las aplicaciones que faltan fueron actualizadas en el sistema CRM.</div>
    <div>Cualquier duda a disposición. Equipo ITDYV.</div>
</div>

</body>
</html>
