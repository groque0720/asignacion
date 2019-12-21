$(".btn-cancelar").click(function(event) {
	event.preventDefault();
	$(".lienzo-unidad").hide();
});

$(".form-unidad").submit(function(event) {
	event.preventDefault();
	$(".mod").show();
	$.ajax({
		url:"guardar_carga_masiva.php",
		cache:false,
		type:"POST",
		data:$(this).serialize(),
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-unidad").hide();
 			$(".contenido-principal").html(result);
		}
	});
});

$("#grupo").change(function(event) {
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