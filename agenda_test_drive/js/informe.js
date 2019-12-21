$("#generar").click(function() {
	if ($("#anio").val()!='') {
		$(".mod").show();
		id_sucursal=$("#id_sucursal").val();
		id_asesor=$("#id_asesor").val();
		id_modelo=$("#id_modelo").val();
		id_mes=$("#id_mes").val();
		anio=$("#anio").val();
		$.ajax({
			url:"informe_cuerpo.php",
			cache:false,
			type:"POST",
			data:{id_sucursal, id_asesor, id_modelo, id_mes, anio},
			success:function(result){
				$(".mod").hide();
	 			$(".zona-tabla-infome").html(result);
			}
		});
	}else{
		swal("Año Incorrecto", "Por Favor verifique el año", "error");
	}
})

$("#id_sucursal").change(function(){
	$(".mod").show();
	id_sucursal=$("#id_sucursal").val();
	$.ajax({
		url:"buscar_asesores.php",
		cache:false,
		type:"POST",
		data:{id_sucursal},
		success:function(result){
			$(".mod").hide();
 			$("#id_asesor").html(result);
		}
	});
});