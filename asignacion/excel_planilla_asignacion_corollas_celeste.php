<?php
// header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
// header('Content-Disposition: attachment; filename=nombre_archivo.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="AsignaciónCorollaDiciembre2020(Celeste)'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
header('Cache-Control: max-age=0');


  include_once("funciones/func_mysql.php");
  conectar();
  //mysqli_query($con,"SET NAMES 'utf8'");

    $SQL="SELECT * FROM view_asignaciones_corollas_celestes";
    $unidades = mysqli_query($con, $SQL);

    $SQL="SELECT * FROM colores ORDER BY color";
    $colores=mysqli_query($con, $SQL);
    $color_a[0]['color']= '-';
    $i=1;
    while ($color=mysqli_fetch_array($colores)) {
      $color_a[$color['idcolor']]['color']= $color['color'];
      $i++;
    }

    $SQL="SELECT * FROM sucursales";
    $sucursales=mysqli_query($con, $SQL);
    $sucursal_a[0]['sucursal']= '-';
    $i=1;
    while ($sucursal=mysqli_fetch_array($sucursales)) {
      $sucursal_a[$i]['sucursal']= $sucursal['sucursal'];
      $i++;
    }

 //$registros = mysqli_num_rows($unidades);

?>

<table border="1">
    <thead>
        <tr><td colspan="18"></td></tr>
        <tr>
            <td colspan="18" style="text-align: center;font-size: 16px; font-weight: bold;">Corolla Diciembre 2020 (Celeste)</td>
        </tr>
        <tr><td colspan="18"></td></tr>
        <tr style="background: #CBC6C6;">
            <td style="text-align: center;">Nro Unidad</td>
            <td style="text-align: center;">Mes</td>
            <td style="text-align: center;">Año</td>
            <td style="text-align: center;">Nro Orden</td>
            <td style="text-align: center;">Interno</td>
            <td style="text-align: center;">Fec. Despacho</td>
            <td style="text-align: center;">Fec. Arribo</td>
            <td style="text-align: center;">Modelo</td>
            <td style="text-align: center;">Versión</td>
            <td style="text-align: center;">Chasis</td>
            <td style="text-align: center;">Color Asignado</td>
            <td style="text-align: center;">Dest. / Ub.</td>
            <td style="text-align: center;">Cancelado</td>
            <td style="text-align: center;">Antiguedad</td>
            <td style="text-align: center;">Cliente</td>
            <td style="text-align: center;">Asesor</td>
            <td style="text-align: center;">Fec. Reserva</td>
            <td style="text-align: center;">Confirmada</td>
        </tr>
    </thead>
    <tbody>
        <?php while ($registro = mysqli_fetch_array($unidades)) { ?>

            <?php

                if ($registro['id_ubicacion'] != '' AND $registro['id_ubicacion'] != null AND $registro['id_ubicacion'] != 0) {
                    $ubicacion = $sucursal_a[$registro['id_ubicacion']]['sucursal'];
                  }else{
                    $ubicacion = $sucursal_a[$registro['id_ubicacion']]['sucursal'];
                  }

                  $color_asignado =$color_a[$registro['id_color']]['color'];

                  if ($registro['cancelada'] == 1) {
                    $cancelado = 'Si';
                  }else{
                    $cancelado = 'No';
                  }

                  if ($registro['estado_reserva'] == 1) {
                    $estado_reserva = 'Si';
                  }else{
                    $estado_reserva = 'No';
                  }

                $dias = '';


                if ($registro['fec_arribo']<>'') {
                    $dias = ((strtotime($registro['fec_arribo'])-strtotime(date("Y/m/d"))))/86400;
                    $dias = abs($dias);
                    $dias = floor($dias);
                }else{
                    $dias = '-';
                }
             ?>
            <tr>
                <td style="text-align: center;"><?php echo $registro['nro_unidad'] ?></td>
                <td><?php echo $registro['mes'] ?></td>
                <td style="text-align: center;"><?php echo $registro['año'] ?></td>
                <td style="text-align: center;"><?php echo $registro['nro_orden'] ?></td>
                <td style="text-align: center;"><?php echo $registro['interno'] ?></td>
                <td style="text-align: center;"><?php echo cambiarFormatoFecha($registro['fec_despacho']) ?></td>
                <td style="text-align: center;"><?php echo cambiarFormatoFecha($registro['fec_arribo']) ?></td>
                <td style="text-align: center;"><?php echo $registro['grupo'] ?></td>
                <td><?php echo $registro['modelo'] ?></td>
                <td style="text-align: center;"><?php echo $registro['chasis'] ?></td>
                <td style="text-align: center;"><?php echo $color_asignado ?></td>
                <td style="text-align: center;"><?php echo $ubicacion ?></td>
                <td style="text-align: center;"><?php echo $cancelado ?></td>
                <td style="text-align: center;"><?php echo $dias ?></td>
                <td><?php echo $registro['cliente'] ?></td>
                <td><?php echo $registro['asesor'] ?></td>
                <td style="text-align: center;"><?php echo cambiarFormatoFecha($registro['fec_reserva']) ?></td>
                <td style="text-align: center;"><?php echo $estado_reserva ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>