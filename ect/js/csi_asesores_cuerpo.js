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
		url:"csi_asesores_actualizar.php",
		cache:false,
		type:"POST",
		data:{id, valor},
		success:function(result){
		}
	});

});	

$(".detalle_csi_asesor").click(function(event){
	event.preventDefault();
	$("#tabla_csi_asesores").hide();
	$("#tabla_csi_asesores_detalle").show();
	$(".mod").show();

	id=$(this).attr('data-id');
	mes=$("#mes").val();
	ano=$("#ano").val();
	asesor=$(this).attr('data-asesor');

	$.ajax({
		url:"csi_asesores_cuerpo_detalle.php",
		cache:false,
		type:"POST",
		data:{id, mes, ano, asesor},
		success:function(result){
			$("#tabla_csi_asesores_detalle").html(result);
			$(".mod").hide();
		}
	});
})