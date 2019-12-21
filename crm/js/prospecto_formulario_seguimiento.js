

$(".fila-seguimiento").click(function(){

	id= $(this).attr('data-id');
	nro=parseInt($('#lienzo_activos').val())+1
	$('#lienzo_activos').val(nro);

	$(".mod").show();
	$.ajax({
		url:'seguimiento_formulario.php',
		cache:false,
		type:"POST",
		data:{id:id},
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-formulario-"+$('#lienzo_activos').val()).html(result);
 			$(".lienzo-formulario-"+$('#lienzo_activos').val()).show();
		}
	})
})