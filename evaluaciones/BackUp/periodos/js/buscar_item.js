tipo_evaluacion = $("#tipo_evaluacion").val();
ter_auto=$("#terminado_autoevaluacion").val();
ter_sup=$("#terminado_superior").val();



if((ter_auto != 0 && tipo_evaluacion=='auto'))  {
	$("#observacion_item").attr('disabled', true);
 }


if((tipo_evaluacion=='supauto' && ter_sup!=0))  {
	$("#observacion_item").attr('disabled', true);
 }


$(".item-calificacion").click(function(event){
	$(this).addClass('item-activo');
});

$(".item").click(function(event){
	
	event.preventDefault();
	// alert($(this).attr('data-idevaluacion')+' '+$(this).attr('data-valor')+' '+$('.zona-clasificacion').attr('data-item'));

	id=$(this).attr('data-idevaluacion');
	valor=$(this).attr('data-valor');

	if ((ter_auto == 0 && tipo_evaluacion=='auto') || (tipo_evaluacion=='supauto' && ter_sup==0)) {


		$('.nroitem-'+$('.zona-clasificacion').attr('data-item')).html('<span class="calificacion-valor">'+$(this).attr('data-valor')+'</span>');
		$(".item-calificacion").removeClass('item-activo');
		$(".item").removeClass('item-activo');
		valor_item=$(this).attr('data-valor');

		$(".div_item_"+valor_item).addClass('item-activo');


		$.ajax({
			url:"guardar_calificacion.php",
			cache:false,
			type:"POST",
			data:{id, valor, tipo_evaluacion},
			success:function(result){
				// $(".lienzo-calificacion").hide();
		    }
		});		
	}else{
		swal('Evaluación Terminada', 'NO se puede hacer cambios', 'info');
	}
});

$("#observacion_item").focusout(function(){
	$(".cerrar-lienzo").focus();
	texto=$(this).val();
	id=$(this).attr('data-idevaluacion');
	tipo_evaluacion = $("#tipo_evaluacion").val();

	// alert(id+ '  '+texto+'  '+tipo_evaluacion);

		$.ajax({
			url:"guardar_observacion.php",
			cache:false,
			type:"POST",
			data:{id, texto, tipo_evaluacion},
			success:function(result){
				// $(".lienzo-calificacion").hide();
		    }
		});

});

$(".cerrar-lienzo").click(function(){
	$(".lienzo-calificacion").hide();
});