<?php
// header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
// header('Content-Disposition: attachment; filename=nombre_archivo.xlsx');

// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="PlanillaAsignación_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
// header('Cache-Control: max-age=0');


  include_once("funciones/func_mysql.php");
  conectar();
  //mysqli_query($con,"SET NAMES 'utf8'");

  $SQL="SELECT * FROM view_asignaciones";
  $unidades = mysqli_query($con, $SQL);

 $registros = mysqli_num_rows($unidades);



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
</table>







<table border="1" cellpadding="2" cellspacing="0" width="100%">
    <caption>Tabla de pruebas</caption>
    <tr>
        <td>Datos</td>
        <td>Datos</td>
        <td>Datos</td>
    </tr>
    <tr>
        <td>Datos</td>
        <td>Datos</td>
        <td>Datos</td>
    </tr>
    <tr>
        <td>Datos</td>
        <td>Datos</td>
        <td>Datos</td>
    </tr>
</table>

