
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	// echo $mes_desde;
	// echo $mes_hasta;
	// echo $ano_desde;
	// echo $ano_hasta;
	// echo $sucursal;
	// echo $asesor;
 $cant_meses='';

	$SQL="SELECT * FROM meses";
	$meses=mysqli_query($con, $SQL);
	$i=1;
	while ($mes=mysqli_fetch_array($meses)) {
		$mes_a[$i]['mes_res']= $mes['mes_res'];
		$i++;
	}

if (($mes_hasta>$mes_desde AND $ano_hasta>=$ano_desde) OR ($ano_hasta>$ano_desde)) {
	

	$cant_meses = $mes_hasta-$mes_desde+1;
	$cant_ano = $ano_hasta - $ano_desde;

    if ($cant_ano>0) {
		$cant_meses = (12 - $mes_desde + $mes_hasta+1)*$cant_ano;
	}

	if ($cant_meses>13) {
		echo 'El periodo es muy largo para analizar';
	}else{
		include('csi_comparativo_cuerpo_detalle.php');
	}

}else{
	echo 'Verifique fechas';
}
 ?>


<!-- 

<div class="ancho-90" id='container'>
	
</div>
<script src="js/highcharts/highcharts.js"></script>

<script>
	Highcharts.chart('container', {

    title: {
        text: 'Solar Employment Growth by Sector, 2010-2016'
    },

    subtitle: {
        text: 'Source: thesolarfoundation.com'
    },

    yAxis: {
        title: {
            text: 'Number of Employees'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            pointStart: 2010
        }
    },

    series: [{
        name: 'Installation',
        data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
    }, {
        name: 'Manufacturing',
        data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
    }, {
        name: 'Sales & Distribution',
        data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
    }, {
        name: 'Project Development',
        data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
    }, {
        name: 'Other',
        data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
    }]

});
</script> -->