$(".btn-cancelar").click(function(event) {
	event.preventDefault();
	$(".lienzo-unidad").hide();
});

$(".form-unidad").submit(function(event) {
	event.preventDefault();
	if (($('#cliente_uno').val()=='NO EXISTE') || ($('#cliente_dos').val()=='NO EXISTE')) {
		swal("Unidad Inexistente", "Por favor verifique los datos para realizar el cambio de unidad", "error");
	}else{
		$(".mod").show();
		$.ajax({
			url:"cambio-unidad-guardar.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			$(".lienzo-unidad").hide();
	 			$(".contenido-principal").html(result);
			}
		});
	}	

});

$("#id_nro_uno").focusout(function(event) {
	if ($(this).val()!='') {
		id_unidad=$(this).val();
		$(".mod").show();
		$.ajax({
			url:"cambio-unidad-buscar.php",
			cache:false,
			type:"POST",
			data:{id_unidad:id_unidad},
			success:function(result){
				$(".mod").hide();
	 			$("#zona_cliente_uno").html(result);
			}
		});
	}else{
		$('#cliente_uno').val('');
	}
});

$("#id_nro_dos").focusout(function(event) {
	if ($(this).val()!='') {
		id_unidad=$(this).val();
		$(".mod").show();
		$.ajax({
			url:"cambio-unidad-buscar.php",
			cache:false,
			type:"POST",
			data:{id_unidad:id_unidad},
			success:function(result){
				$(".mod").hide();
	 			$("#zona_cliente_dos").html(result);
			}
		});
	}else{
		$('#cliente_dos').val('');
	}	
})