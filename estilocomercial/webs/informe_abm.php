        <script>

            $(".ver_informe").click(function(event){

                event.preventDefault();
                mes=$("#mes").val();
                año=$("#año").val();
                id_encuesta=$(this).attr("data-enc");
                id_sucursal=$("#sucursales").val();
                id_asesor=$("#asesor").val();
                cant_realizado=$(this).attr("data-cant");

                if (id_encuesta==1) {
                    url="informe_nocompra.php?mes="+mes+"&año="+año+"&suc="+id_sucursal+"&ase="+id_asesor+"&cant="+cant_realizado;
                };
                if (id_encuesta==2) {
                    url="informe_satisfaccion.php?mes="+mes+"&año="+año+"&suc="+id_sucursal+"&ase="+id_asesor+"&cant="+cant_realizado;
                };
                if (id_encuesta==3) {
                    url="informe_servicio.php?mes="+mes+"&año="+año+"&suc="+id_sucursal+"&ase="+id_asesor+"&cant="+cant_realizado;
                };

                window.open (url);
            });

            $(".ver").click(function(event){
                event.preventDefault();
                mes=$("#mes").val();
                año=$("#año").val();
                id_sucursal=$("#sucursales").val();
                id_asesor=$("#asesor").val();
                cant_realizado=$(this).attr("data-cant");
                extra=$(this).attr("data-cons");
                encuesta = $(this).attr("data-enc");
                estado = $(this).attr("data-est");

                url="informe_encuestas_reporte.php?mes="+mes+"&año="+año+"&suc="+id_sucursal+"&ase="+id_asesor+"&cant="+cant_realizado+"&extra="+extra+"&encuesta="+encuesta+"&estado="+estado;


                window.open (url);
            });
</script>
<?php
	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");

	extract($_POST);
//--Buscar Asesores----------------------------------------------------------------------------------------
	if ($_POST["operacion"]=="buscar_asesores") { ?>

		<label for="asesor">Asesor</label>
		<select name="asesor" id="asesor">
			<option class="top" value="0">Todos de la Suc.</option>
			<?php
			$SQL="SELECT * FROM usuarios WHERE id_perfil = 2 AND activo = 1 AND id_sucursal = ".$_POST["id_sucursal"];
			$usuarios=mysqli_query($con, $SQL);
			while ($usu=mysqli_fetch_array($usuarios)) { ?>
				<option value="<?php echo $usu["id_usuario"]; ?>"><?php echo $usu["nombre"] ?></option>
			<?php } ?>
		</select>
	<?php }  ?>

	<?php
//--Reporte General------------------------------------------------------------------------------------------------------------

	if ($operacion=="reporte_gral") {

		$cad='';
		if ($mes!=0) {
			$cad .=" AND MONTH(fecha_muestra_origen) = ".$mes;
		}
		if ($id_sucursal!=0) {
			$cad .=" AND id_sucursal = ".$id_sucursal;
		}
		if ($id_asesor!=0) {
			$cad .=" AND id_asesor =".$id_asesor;
		}
		$SQL="UPDATE cuestionarios SET id_usuario = 22 WHERE (id_encuesta = 3)";
		mysqli_query($con, $SQL);
		?>
		<div class="zona-tabla-70">
			<?php
			if ($id_encuesta!=0) {
				$SQL="SELECT * FROM encuestas WHERE activo =1 AND id_encuesta = ".$id_encuesta;
			}else{
				$SQL="SELECT * FROM encuestas WHERE activo =1";
			}
				$encuestas=mysqli_query($con, $SQL);
			 ?>

			<table class="tabla-default">
				<thead>
					<tr>
						<td width="20%">Encuesta</td>
						<td width="10%">Muestra</td>
						<td width="10%">Pendientes</td>
						<td width="10%">Realizadas</td>
						<td width="5%">% Realizadas</td>
						<td width="10%">Cerradas</td>
						<td width="10%">Observadas</td>
<!-- 						<td width="10%">%/Total</td>
						<td width="10%">%/Realizadas</td> -->
						<td width="10%">Detalle de Informe</td>

					</tr>
				</thead>

				<tbody>
					<?php
                    $cont_enc=0;
						while ($enc=mysqli_fetch_array($encuestas)) { $cont_enc = $cont_enc +1;
							$SQL="SELECT count(*) as cantidad FROM view_cuestionario_gral WHERE id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							$cuestionarios=mysqli_query($con, $SQL);
							$cue=mysqli_fetch_array($cuestionarios);
							$cant_muestra = $cue["cantidad"];
							$i=(int)$enc['id_encuesta'];
							$array[$i]['muestra']=$cant_muestra;
							$cad_paso = "id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							?>
						<tr>
							<td><div class="centrar-texto"><?php echo $enc["encuesta"]; ?></div></td>

							<td><div class="centrar-texto"><a class="ver cursor" data-est="Muestra" data-enc="<?php echo $enc["encuesta"]; ?>" data-cons="<?php echo $cad_paso; ?>" data-cant="<?php echo $cant_muestra; ?>"  ><?php echo $cant_muestra; ?></a></div> </td>

							<?php
							$SQL="SELECT count(*) as cantidad FROM view_cuestionario_gral WHERE id_estado_cuestionario < 3 AND motivo=0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							$cuestionarios=mysqli_query($con, $SQL);
							$cue=mysqli_fetch_array($cuestionarios);
							$cant_pendiente= $cue["cantidad"];
							$array[$i]['pendiente']=$cant_pendiente;
							$cad_paso ="id_estado_cuestionario < 3 AND motivo=0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							 ?>
							<td><div class="centrar-texto"><a class="ver cursor" data-est="Pendientes" data-enc="<?php echo $enc["encuesta"]; ?>"  data-cons="<?php echo $cad_paso; ?>" data-cant="<?php echo $cant_pendiente; ?>" ><?php echo $cant_pendiente; ?></a></div> </td>

							<?php
							$SQL="SELECT count(*) as cantidad FROM view_cuestionario_gral WHERE id_estado_cuestionario = 3 AND motivo=0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							$cuestionarios=mysqli_query($con, $SQL);
							$cue=mysqli_fetch_array($cuestionarios);
							$cant_realizado= $cue["cantidad"];
							$array[$i]['realizado']=$cant_realizado;
							$cad_paso = "id_estado_cuestionario = 3 AND motivo=0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							 ?>
							<td><div class="centrar-texto"><a class="ver cursor" data-est="Realizadas" data-enc="<?php echo $enc["encuesta"]; ?>" data-cons="<?php echo $cad_paso; ?>" data-cant="<?php echo $cant_realizado; ?>" ><?php echo $cant_realizado; ?></a></div></td>

							<?php
                            if ($cant_muestra!=0) {
                               $array[$i]['porcentaje']=round(($cant_realizado*100)/$cant_muestra,2);
                            }else{
                                $array[$i]['porcentaje']=0;
                            }

							 ?>
							<td><div class="negrita derecha-texto"><?php  if ($cant_muestra!=0) { echo round(($cant_realizado*100)/$cant_muestra,2)." %";}else{ echo " 0 %";} ?></div></td>

							<?php
							$SQL="SELECT count(*) as cantidad FROM view_cuestionario_gral WHERE id_estado_cuestionario = 3 AND motivo>0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							$cuestionarios=mysqli_query($con, $SQL);
							$cue=mysqli_fetch_array($cuestionarios);
							$cant_no_realizado= $cue["cantidad"];
							$array[$i]['cerradas']=$cant_no_realizado;
							$cad_paso = "id_estado_cuestionario = 3 AND motivo>0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							 ?>
							<td><div class="centrar-texto"><a class="ver cursor" data-est="Cerradas" data-enc="<?php echo $enc["encuesta"]; ?>" data-cons="<?php echo $cad_paso; ?>" data-cant="<?php echo $cant_no_realizado; ?>" ><?php echo $cant_no_realizado; ?></a></div></td>

							<?php
							$SQL="SELECT count(*) as cantidad FROM view_cuestionario_gral WHERE caracter= 1 AND id_estado_cuestionario = 3 AND motivo=0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							$cuestionarios=mysqli_query($con, $SQL);
							$cue=mysqli_fetch_array($cuestionarios);
							$cant_obs=$cue["cantidad"];
							$array[$i]['obs']=$cant_obs;
							$cad_paso="caracter= 1 AND id_estado_cuestionario = 3 AND motivo=0 AND id_encuesta =".$enc["id_encuesta"]." AND YEAR(fecha_muestra_origen) =".$año.$cad;
							 ?>

							<td><div class="centrar-texto"><a class="ver cursor" data-est="Observadas" data-enc="<?php echo $enc["encuesta"]; ?>" data-cons="<?php echo $cad_paso; ?>" data-cant="<?php echo $cant_obs; ?>"><?php echo $cant_obs; ?></a></div></td>
<!-- 							<td><div class="derecha-texto"><?php  if ($cant_muestra!=0) { echo round(($cant_obs*100)/$cant_muestra,2)." %";}else{ echo " 0 %";} ?></div></td>
							<td><div class="derecha-texto"><?php  if ($cant_realizado!=0) { echo round(($cant_obs*100)/$cant_realizado,2)." %";}else{ echo " 0 %";} ?></div></td> -->
							<td><div class="centrar-texto"><a href="" data-cant="<?php echo $cant_realizado; ?>" data-enc="<?php echo $enc["id_encuesta"]; ?>" class="icon-buscar espacio ver_informe"></a></div></td>
						</tr>

					<?php } ?>

				</tbody>

			</table>
		</div>
		<hr>
<?php if ($cont_enc==3) {  ?>
          <?php
          $tot_muestra=0;
          for ($c=1; $c <= $i; $c++) {
              $tot_muestra = $tot_muestra + $array[$c]['muestra'];
          }
          for ($c=1; $c <= $i; $c++) {
              $array[$c]['porc_s_total']= round($array[$c]['muestra'] * 100 / $tot_muestra,2);
          }

            ?>

    		<div class="ed-container">
    			<div class="ed-item web-50">
    				<div id="container_uno">
    				</div>
    			</div>
    			<div class="ed-item web-50">
    				<div id="container">
    				</div>
    			</div>
            </div>
<?php } ?>



<script>
			// ---- Script grafico uno

	$('#container_uno').highcharts({
       chart: {
            type: 'column'
        },
        title: {
            text: 'Porcentajes de Encuestas realizadas sobre Total de Muestra'
        },
        subtitle: {
            text: '<a href="#"></a>.'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Pocentajes'
            }

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.1f}%'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> de la Muestra<br/>'
        },

        series: [{
            name: 'Encuesta',
            colorByPoint: true,
            data: [{
                name: 'No Compra',
                y: <?php echo $array[1]['porcentaje']; ?>,
                drilldown: 'No Compra'
            }, {
                name: 'Satisfacción',
                y: <?php echo $array[2]['porcentaje']; ?>,
                drilldown: 'Satisfacción'
            }, {
                name: 'Servicio',
                y: <?php echo $array[3]['porcentaje']; ?>,
                drilldown: 'Servicio'
            }]
        }],

    });


//---- script Grafico 2
			var colors = Highcharts.getOptions().colors,
        	categories = ['No Compra', 'Satisfacción', 'Servicio'],
        	data = [{
            y: <?php echo $array[1]['muestra']; ?>,
            color: colors[0],
            drilldown: {
                name: 'No Compra',
                categories: ['Pendientes','Realizadas', 'Cerradas'],
                data: [<?php echo $array[1]['pendiente'] ?>, <?php echo $array[1]['realizado'] ?>, <?php echo $array[1]['cerradas'] ?>],
                color: colors[0]
            }
        }, {
            y: <?php echo $array[2]['muestra']; ?>,
            color: colors[1],
            drilldown: {
                name: 'Satisfacción',
                categories: ['Pendientes','Realizadas', 'Cerradas'],
                data: [<?php echo $array[2]['pendiente'] ?>, <?php echo $array[2]['realizado'] ?>, <?php echo $array[2]['cerradas'] ?>],
                color: colors[1]
            }
        }, {
            y: <?php echo $array[3]['muestra']; ?>,
            color: colors[2],
            drilldown: {
                name: 'Servicio',
                categories: ['Pendientes','Realizadas', 'Cerradas'],
                data: [<?php echo $array[3]['pendiente'] ?>, <?php echo $array[3]['realizado'] ?>, <?php echo $array[3]['cerradas'] ?>],
                color: colors[2]
            }


        }],
        browserData = [],
        versionsData = [],
        i,
        j,
        dataLen = data.length,
        drillDataLen,
        brightness;


    // Build the data arrays
    for (i = 0; i < dataLen; i += 1) {

        // add browser data
        browserData.push({
            name: categories[i],
            y: data[i].y,
            color: data[i].color
        });

        // add version data
        drillDataLen = data[i].drilldown.data.length;
        for (j = 0; j < drillDataLen; j += 1) {
            brightness = 0.2 - (j / drillDataLen) / 5;
            versionsData.push({
                name: data[i].drilldown.categories[j],
                y: data[i].drilldown.data[j],
                color: Highcharts.Color(data[i].color).brighten(brightness).get()
            });
        }
    }

    // Create the chart

    $('#container').highcharts({
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Cantidad de Encuestas y Respectivos Estados'
        },
        subtitle: {
            text: ''
        },
        yAxis: {
            title: {
                text: 'Total percent market share'
            }
        },
        plotOptions: {
            pie: {
                shadow: false,
                center: ['50%', '50%']
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        series: [{
            name: 'Cantidad',
            data: browserData,
            size: '60%',
            dataLabels: {
                formatter: function () {
                    return this.y > 5 ? this.point.name : null;
                },
                color: '#ffffff',
                distance: -30
            }
        }, {
            name: 'Cantidad',
            data: versionsData,
            size: '80%',
            innerSize: '60%',
            dataLabels: {
                formatter: function () {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>' + this.point.name + ':</b> ' + this.y + '' : null;
                }
            }
        }]
    });

</script>

	<?php } ?>


<?php mysqli_close($con); ?>