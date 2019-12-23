<?php
// header('Content-type: application/vnd.ms-excel;charset=iso-8859-15');
// header('Content-Disposition: attachment; filename=nombre_archivo.xlsx');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PlanillaAsignación_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
header('Cache-Control: max-age=0');
?>

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

