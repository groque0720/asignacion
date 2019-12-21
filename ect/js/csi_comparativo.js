$("#zona_asesor").hide();

$("#sucursal").change(function(){

	if ($(this).val()==0) {
		$("#zona_asesor").hide();
	}else{
		$(".mod").show();
		id_sucursal = $(this).val();

		$.ajax({
		url:"csi_comparativo_buscar_asesores.php",
		cache:false,
		type:"POST",
		data:{id_sucursal},
		success:function(result){
 			$("#asesor").html(result);
 			$("#zona_asesor").show();
 			$(".mod").hide();
			}
		})
	}

});


$(".definir").change(function(){

	if ($("#mes_desde").val()!=0 && $("#ano_desde").val()!='' && $("#mes_hasta").val()!=0 && $("#ano_hasta").val()!='') {

		$(".mod").show();

		mes_desde = $("#mes_desde").val();
		mes_hasta = $("#mes_hasta").val();
		ano_hasta = $("#ano_hasta").val();
		ano_desde = $("#ano_desde").val();
		sucursal = $("#sucursal").val();
		asesor = $("#asesor").val();

		$.ajax({
		url:"csi_comparativo_cuerpo.php",
		cache:false,
		type:"POST",
		data:{mes_desde, mes_hasta, ano_desde, ano_hasta, sucursal, asesor},
		success:function(result){
 			$("#zona_comparativo").html(result);
 			$(".mod").hide();
			}
		})

	}

});

$("#imprimir_comparativo").click(function (event) {
	event.preventDefault();

    var chart = $('#grafico_comparativo_csi').highcharts();

	chart.setSize(600,240, false);
    //chart.print();
    setTimeout(function() {
        chart.setSize(600,240, true);
    }, 1000);

	window.print();
});