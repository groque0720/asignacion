//$("#canvas_img_ventas").hide();

$("#imprimir").click(function (event) {
	event.preventDefault();

    var chart = $('#grafico_ventas_acumuladas').highcharts();

	chart.setSize(240,200, false);
    //chart.print();
    setTimeout(function() {
        chart.setSize(370,240, true);
    }, 1000);

    // ------------------------------------------
    
    var chart_01 = $('#grafico_ventas_mensuales').highcharts();

	chart_01.setSize(275,245, false);
    //chart.print();
    setTimeout(function() {
        chart_01.setSize(455,290, true);
    }, 1000);

	window.print();
});

$(".definir").change(function(){

    mes=$("#mes").val();
    ano=$("#ano").val();
    id_asesor = $("#asesor").val();


    if (mes!=0 && ano!='' && ano >= 2016 && ano < 2020 && id_asesor != 0) {

        $(".mod").show();
        $.ajax({
            url:"plan_mensual_individual_cuerpo.php",
            cache:false,
            type:"POST",
            data:{mes, ano, id_asesor},
            success:function(result){
                $("#zona_definicion_pmi").html(result);
                $(".mod").hide();
            }
        });

    };

    if (ano=='' || ano < 2016 || ano > 2020) {
        swal("Datos Inválidos", "Por favor verifique los datos para realizar la creacion de PMI", "error");
    }
});
