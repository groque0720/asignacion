$(".fila").click(function(){

	id_recepcion = $(this).attr('data-id');

	$(".mod").show();
	$.ajax({
		url:'registro_formulario.php',
		cache:false,
		type:"POST",
		data:{id_recepcion:id_recepcion},
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-formulario").html(result);
  		$(".lienzo-formulario").show();
		}
	})
});


