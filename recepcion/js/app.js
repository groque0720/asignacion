$(document).ready(function(){
	$(".mod").hide();
	$(".lienzo-formulario").hide();
	$("#imagen_carga").hide();

	$("#boton_ver_mas").click(function(){

		$("#imagen_carga").show();
		$(this).hide();
		final=parseInt($(this).attr("data-cantidad"));
		inicio=parseInt($(this).attr("data-ini"))+final;
		$(this).attr("data-ini",inicio);
		//$('#tabla-recepcion tr:last').after('<tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr><tr><td>Cuatro</td></tr>');

		$.ajax({
			url:'recepcion_cuerpo_paginacion.php',
			cache:false,
			type:"POST",
			data:{inicio:inicio, final:final},
			success:function(result){
	 			$('#tabla-recepcion tr:last').after(result);
	 			$("#imagen_carga").hide();
				$("#boton_ver_mas").show();
			}
		})
	});

	$("#btn_filtro_fecha").click(function(){
		// $url='reporte_recepcion.php';
		// window.location.href = 'reporte_recepcion.php';
		if ($("#id_sucursal").val() == 2) {
			id_sucursal = $("#select_suc").val();
		}else{
			id_sucursal = $("#id_sucursal").val();
		}
		fecha = $('#fecha_filtro').val();

		window.open('reporte_recepcion.php?id='+id_sucursal+'&fecha='+fecha);
	});

	$("#btn_filtro_fecha_nc").click(function(){
		// $url='reporte_recepcion.php';
		// window.location.href = 'reporte_recepcion.php';
		if ($("#id_sucursal").val() == 2) {
			id_sucursal = $("#select_suc").val();
		}else{
			id_sucursal = $("#id_sucursal").val();
		}
		fecha = $('#fecha_filtro').val();

		window.open('reporte_recepcion_no_compra.php?id='+id_sucursal+'&fecha='+fecha);
	});


})