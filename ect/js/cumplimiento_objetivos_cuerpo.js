$(".cuadro-input").focusin(function(){
	$(".cuadro-input").removeClass('fondo-rojo-1');
	$(this).addClass('fondo-rojo-1');
	this.select();

	// $(".filas").removeClass('fondo-azul-2');
	// $("."+$(this).attr('data-fila')).addClass('fondo-azul-2');
})


$(".cuadro-input").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);

	  if(keycode == '13'){

	  	fila=parseInt($(this).attr('data-nrofila'))+1;
	  	columna = $(this).attr('data-columna');

	  	$("."+fila+'-'+columna).focus();

	  }

});

$(".cuadro-input").focusout(function(){

	id=$(this).attr('data-id');
	valor=$(this).val();


	$.ajax({
		url:"cumplimiento_objetivos_actualizar.php",
		cache:false,
		type:"POST",
		data:{id, valor},
		success:function(result){
		}
	});

});	