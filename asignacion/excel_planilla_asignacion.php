<?php


  include_once("funciones/func_mysql.php");
  conectar();
  mysqli_query($con,"SET NAMES 'utf8'");

  $SQL="SELECT * FROM view_asignaciones";
  $unidades = mysqli_query($con, $SQL);

 $registros = mysqli_num_rows($unidades);

 if ($registros > 0) {
   require_once 'Classes/PHPExcel.php';
   $objPHPExcel = new PHPExcel();

   //Informacion del excel
   $objPHPExcel->
    getProperties()
        ->setCreator("Roque Gómez")
        ->setLastModifiedBy("Roque Gómez")
        ->setTitle("Derka y Vargas S. A.")
        ->setSubject("Planilla de Asignación")
        ->setDescription("Documento generado en RoqueSystem")
        ->setKeywords("RoqueSystem")
        ->setCategory("Asignación");

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
      $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','Nro Unidad')
            ->setCellValue('B1','Mes')
            ->setCellValue('C1','Año')
            ->setCellValue('D1','Nro Orden')
            ->setCellValue('E1','Interno')
            ->setCellValue('F1','Fec. Despacho')
            ->setCellValue('G1','Fec. Arribo')
            ->setCellValue('H1','Modelo')
            ->setCellValue('I1','Versión')
            ->setCellValue('J1','Chasis')
            ->setCellValue('K1','Color Asignado')
            ->setCellValue('L1','Dest. / Ub.')
            ->setCellValue('M1','Cancelado')
	    ->setCellValue('N1','Antiguedad')
            ->setCellValue('O1','Cliente')
            ->setCellValue('P1','Asesor')
            ->setCellValue('Q1','Fec. Reserva')
            ->setCellValue('R1','Confirmada');

   $i = 2;

   while ($registro = mysqli_fetch_object ($unidades)) {

      if ($registro->id_ubicacion != '' AND $registro->id_ubicacion != null AND $registro->id_ubicacion != 0) {
        $ubicacion = $sucursal_a[$registro->id_ubicacion]['sucursal'];
      }else{
        $ubicacion = $sucursal_a[$registro->id_ubicacion]['sucursal'];
      }

      $color_asignado =$color_a[$registro->id_color]['color'];

      if ($registro->cancelada == 1) {
        $cancelado = 'Si';
      }else{
        $cancelado = 'No';
      }

      if ($registro->estado_reserva == 1) {
        $estado_reserva = 'Si';
      }else{
        $estado_reserva = 'No';
      }

				$dias = '';


				if ($registro->fec_arribo<>'') {
					$dias = ((strtotime($registro->fec_arribo)-strtotime(date("Y/m/d"))))/86400;
					$dias = abs($dias);
					$dias = floor($dias);
				}else{
					$dias = '-';
				}


      $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $registro->nro_unidad)
            ->setCellValue('B'.$i, $registro->mes)
            ->setCellValue('C'.$i, $registro->año)
            ->setCellValue('D'.$i, $registro->nro_orden)
            ->setCellValue('E'.$i, $registro->interno)
            ->setCellValue('F'.$i, cambiarFormatoFecha($registro->fec_despacho))
            ->setCellValue('G'.$i, cambiarFormatoFecha($registro->fec_arribo))
            ->setCellValue('H'.$i, $registro->grupo)
            ->setCellValue('I'.$i, $registro->modelo)
            ->setCellValue('J'.$i, $registro->chasis)
            ->setCellValue('K'.$i, $color_asignado)
            ->setCellValue('L'.$i, $ubicacion)
            ->setCellValue('M'.$i, $cancelado)
	    ->setCellValue('N'.$i, $dias)
            ->setCellValue('O'.$i, $registro->cliente)
            ->setCellValue('P'.$i, $registro->asesor)
            ->setCellValue('Q'.$i, cambiarFormatoFecha($registro->fec_reserva))
            ->setCellValue('R'.$i, $estado_reserva);
      $i++;
   }
}
//header('Content-Type: application/vnd.ms-excel');
//header('Content-Disposition: attachment;filename="PlanillaAsignación_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xls"');
//header('Cache-Control: max-age=0');


//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//header('Content-Disposition: attachment;filename="Reportedealumnos.xls"');
//header('Cache-Control: max-age=0');

$objPHPExcel->setActiveSheetIndex(0);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PlanillaAsignación_'.date("d-m-Y").'_'.date("H").date("i").date("s").'".xlsx"');
header('Cache-Control: max-age=0');



$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
$objWriter->save('php://output');
exit;
mysql_close ();
?>