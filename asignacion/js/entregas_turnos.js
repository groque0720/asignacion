$(".celda_turno").click(function(){
	//alert($(this).attr('data-horario')+ ' '+$(this).attr('data-turno')+ ' '+$(this).attr('data-fecha'));
	id_turno = $(this).attr('data-turno');
	horario = $(this).attr('data-horario');
	fecha = $(this).attr('data-fecha');
	nro_unidad = $(this).attr('data-nrounidad');
	id_sucursal = $("#sucursal_agenda").val();
	id = $(this).attr('data-idturno');
		$(".mod").show();
		$.ajax({
			url:"entregas_agenda_formulario.php",
			cache:false,
			type:"POST",
			data:{id_turno,horario,fecha,nro_unidad, id_sucursal, id},
			success:function(result){
				$(".mod").hide();
     			$(".lienzo-unidad").html(result);
      			$(".lienzo-unidad").show();
    		}
    	});
	});


$("#sucursal_agenda").change(function(){

	id_sucursal = $(this).val();
	$(".mod").show();
	$.ajax({
		url:"entregas_agenda_contenido_relleno_cuerpo_tabla.php",
		cache:false,
		type:"POST",
		data:{id_sucursal},
		success:function(result){
			$(".mod").hide();
 			$(".agenda-tabla").html(result);

		}
	});

})