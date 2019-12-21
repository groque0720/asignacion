$(document).ready(function(){

	$("#zona_asesor").hide();

	//-------------------------------

	$("#sucursales").change(function(){
		if ($(this).val()==0) {
			$("#zona_asesor").hide(50);
			$("#asesor").val(0);
		}else{
			operacion="buscar_asesores";
			id_sucursal=$(this).val();
			$.ajax({
				url:"informe_abm.php",
				cache:false,
				type:"POST",
				data:{operacion:operacion, id_sucursal:id_sucursal},
				success:function(result){
					$("#zona_asesor").html(result);
					$("#zona_asesor").show(50);
	    		}
	    	});
	    };
	})

	//-----------------------------------------------------------
	
	$("#generar_reporte").click(function(event){
		event.preventDefault();
		mes=$("#mes").val();
		año=$("#año").val();
		id_encuesta=$("#encuesta").val();
		id_sucursal=$("#sucursales").val();
		id_asesor=$("#asesor").val();
		operacion="reporte_gral";

		$.ajax({
			url:"informe_abm.php",
			cache:false,
			type:"POST",
			data:{
				operacion:operacion,
				mes:mes,
				año:año,
				id_encuesta:id_encuesta,
				id_sucursal:id_sucursal,
				id_asesor:id_asesor
				},
			success:function(result){
				$("#zona_informe_ajax").html(result);
    		}
    	});
		//-------------------------------------------------------



	})	


})