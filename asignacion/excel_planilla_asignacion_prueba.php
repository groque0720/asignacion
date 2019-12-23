<?php
header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
// header('Content-Disposition: attachment; filename=nombre_archivo.xlsx');

// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PlanillaAsignación_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
header('Cache-Control: max-age=0');


  include_once("funciones/func_mysql.php");
  conectar();
  //mysqli_query($con,"SET NAMES 'utf8'");

    $SQL="SELECT * FROM view_asignaciones";
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

<table>
    <thead>
        <tr>
            <td>Nro Unidad</td>
            <td>Mes</td>
            <td>Año</td>
            <td>Nro Orden</td>
            <td>Interno</td>
            <td>Fec. Despacho</td>
            <td>Fec. Arribo</td>
            <td>Modelo</td>
            <td>Versión</td>
            <td>Chasis</td>
            <td>Color Asignado</td>
            <td>Dest. / Ub.</td>
            <td>Cancelado</td>
            <td>Antiguedad</td>
            <td>Cliente</td>
            <td>Asesor</td>
            <td>Fec. Reserva</td>
            <td>Confirmada</td>
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
                <td><?php echo $registro['nro_unidad'] ?></td>
                <td><?php echo $registro['mes'] ?></td>
                <td><?php echo $registro['año'] ?></td>
                <td><?php echo $registro['nro_orden'] ?></td>
                <td><?php echo $registro['interno'] ?></td>
                <td><?php echo cambiarFormatoFecha($registro['fec_despacho']) ?></td>
                <td><?php echo cambiarFormatoFecha($registro['fec_arribo']) ?></td>
                <td><?php echo $registro['grupo'] ?></td>
                <td><?php echo $registro['modelo'] ?></td>
                <td><?php echo $registro['chasis'] ?></td>
                <td><?php echo $color_asignado ?></td>
                <td><?php echo $ubicacion ?></td>
                <td><?php echo $cancelado ?></td>
                <td><?php echo $dias ?></td>
                <td><?php echo $registro['cliente'] ?></td>
                <td><?php echo $registro['asesor'] ?></td>
                <td><?php echo cambiarFormatoFecha($registro['fec_reserva']) ?></td>
                <td style="text-align: center;"><?php echo $estado_reserva ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>