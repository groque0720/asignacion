$(".fila").click(function(){

	id= $(this).attr('data-id');

	$(".mod").show();
	$.ajax({
		url:'cliente_formulario.php',
		cache:false,
		type:"POST",
		data:{id:id},
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-formulario").html(result);
  		$(".lienzo-formulario").show();
		}
	})
});



