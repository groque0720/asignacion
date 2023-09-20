  <?php
// header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
// header('Content-Disposition: attachment; filename=nombre_archivo.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="EstadoReventaPatente_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
header('Cache-Control: max-age=0');


  include_once("funciones/func_mysql.php");
  conectar();
  //mysqli_query($con,"SET NAMES 'utf8'");

    $SQL="SELECT * FROM view_asignaciones_reventa_patentamiento";
    $unidades = mysqli_query($con, $SQL);


 //$registros = mysqli_num_rows($unidades);

?>

<table border="1">
    <thead>
        <tr style="background: #CBC6C6;">
            <td style="text-align: center;">Nro Unidad</td>
            <td style="text-align: center;">Mes</td>
            <td style="text-align: center;">Año</td>
            <td style="text-align: center;">Interno</td>
            <td style="text-align: center;">Fec. Arribo</td>
            <td style="text-align: center;">Modelo</td>
            <td style="text-align: center;">Versión</td>
            <td style="text-align: center;">Cancelado</td>
            <td style="text-align: center;">Antiguedad</td>
            <td style="text-align: center;">Cliente</td>
            <td style="text-align: center;">Asesor</td>
            <td style="text-align: center;">Facturada</td>
            <td style="text-align: center;">A nombre</td>
            <td style="text-align: center;">Nombre Factura</td>
            <td style="text-align: center;">Patente</td>
        </tr>
    </thead>
    <tbody>
        <?php while ($registro = mysqli_fetch_array($unidades)) { ?>

            <?php 

                  if ($registro['cancelada'] == 1) {
                    $cancelado = 'Si';
                  }else{
                    $cancelado = 'No';
                  }

                if ($registro['facturada'] == 1) {
                    $facturada = 'Si';
                  }else{
                    $facturada = 'No';
                  }
             ?>
            <tr>
                <td style="text-align: center;"><?php echo $registro['nro_unidad'] ?></td>
                <td><?php echo $registro['mes'] ?></td>
                <td style="text-align: center;"><?php echo $registro['año'] ?></td>
                <td style="text-align: center;"><?php echo $registro['interno'] ?></td>
                <td style="text-align: center;"><?php echo cambiarFormatoFecha($registro['fec_arribo']) ?></td>
                <td style="text-align: center;"><?php echo $registro['grupo'] ?></td>
                <td><?php echo $registro['modelo'] ?></td>
                <td style="text-align: center;"><?php echo $cancelado ?></td>
                <td><?php echo $registro['antiguedad'] ?></td>
                <td><?php echo $registro['cliente'] ?></td>
                <td><?php echo $registro['asesor'] ?></td>
                <td style="text-align: center;"><?php echo $facturada ?></td>
                <td style="text-align: center;"><?php echo $registro['anombre'] ?></td>
                <td style="text-align: center;"><?php echo $registro['nombre'] ?></td>
                <td style="text-align: center;"><?php echo $registro['patente'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>