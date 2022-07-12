<?php
// header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
// header('Content-Disposition: attachment; filename=nombre_archivo.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PlanillaAsignaciónSeña_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
header('Cache-Control: max-age=0');


  include_once("funciones/func_mysql.php");
  conectar();

    $SQL="SELECT * FROM asignacion_activo_seña";
    $unidades = mysqli_query($con, $SQL);

    // $SQL="SELECT * FROM colores ORDER BY color";
    // $colores=mysqli_query($con, $SQL);
    // $color_a[0]['color']= '-';
    // $i=1;
    // while ($color=mysqli_fetch_array($colores)) {
    //   $color_a[$color['idcolor']]['color']= $color['color'];
    //   $i++;
    // }

    // $SQL="SELECT * FROM sucursales";
    // $sucursales=mysqli_query($con, $SQL);
    // $sucursal_a[0]['sucursal']= '-';
    // $i=1;
    // while ($sucursal=mysqli_fetch_array($sucursales)) {
    //   $sucursal_a[$i]['sucursal']= $sucursal['sucursal'];
    //   $i++;
    // }


?>

<table border="1">
    <thead>
        <tr style="background: #CBC6C6;">
            <td style="text-align: center;">Nro Unidad</td>
            <td style="text-align: center;">Mes</td>
            <td style="text-align: center;">Año</td>
            <!-- <td style="text-align: center;">Nro Orden</td> -->
            <!-- <td style="text-align: center;">Interno</td> -->
            <!-- <td style="text-align: center;">Fec. Despacho</td> -->
            <!-- <td style="text-align: center;">Fec. Arribo</td> -->
            <td style="text-align: center;">Modelo</td>
            <td style="text-align: center;">Versión</td>
            <!-- <td style="text-align: center;">Chasis</td> -->
            <!-- <td style="text-align: center;">Color Asignado</td> -->
            <!-- <td style="text-align: center;">Dest. / Ub.</td> -->
            <!-- <td style="text-align: center;">Cancelado</td> -->
            <!-- <td style="text-align: center;">Antiguedad</td> -->
             <td style="text-align: center;">Fec. Reserva</td>
            <td style="text-align: center;">Cliente</td>
            <!-- <td style="text-align: center;">Asesor</td> -->
            <td style="text-align: center;">Detalle</td>
            <td style="text-align: center;">Monto Seña</td>
        </tr>
    </thead>
    <tbody>
        <?php while ($registro = mysqli_fetch_array($unidades)) { ?>

            <tr>
                <td style="text-align: center;"><?php echo $registro['nro_unidad'] ?></td>
                <td style="text-align: center;"><?php echo $registro['mes'] ?></td>
                <td style="text-align: center;"><?php echo $registro['año'] ?></td>
                <td style="text-align: center;"><?php echo $registro['grupo'] ?></td>
                <td style="text-align: center;"><?php echo $registro['modelo'] ?></td>
                <td style="text-align: center;"><?php echo cambiarFormatoFecha($registro['fec_reserva']) ?></td>
                <td><?php echo $registro['cliente'] ?></td>
                <td style="text-align: center;"><?php echo $registro['detalle'] ?></td>
                <td style="text-align: center;"><?php echo number_format( $registro['monto'], 2, ',','.') ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>