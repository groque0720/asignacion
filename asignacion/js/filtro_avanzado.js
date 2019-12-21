$(document).ready(function(){

	$("#hasta").hide();
	
	$("#id_grupo").change(function(event) {
		grupo=$(this).val();
		$(".mod").show();
		$.ajax({
			url:"buscar_modelos.php",
			cache:false,
			type:"POST",
			data:{grupo:grupo},
			success:function(result){
					$(".mod").hide();
					$("#id_modelo").html(result);
				}
		});
	});


	$("#filtro_mes").change(function(event) {
		if ($(this).val()==4) {
			$("#hasta").show(200);
		}else{
			$("#hasta").hide(200);
		}
	});

	$("#form_filtro").submit(function(event) {
		event.preventDefault();

		if ($("#filtro_mes").val()==4 && $("#mes_desde").val()==0 && $("#año_desde").val()!='' && $("#mes_hasta").val()==0 && $("#año_hasta").val()!='') {
				swal("Referencias Incompletas", "Defina los parametros de Meses a Buscar", "error");
		}
		
		$(".mod").show();
		$.ajax({
			url:"filtro_avanzado_busqueda.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
	 			$("#resultado_filtro").html(result);
	 			setTimeout(function (){$(".lienzo-unidad").hide();}, 2500);
	 			setTimeout(function (){$(".mod").hide();}, 2500);
			}
		});
	});

})