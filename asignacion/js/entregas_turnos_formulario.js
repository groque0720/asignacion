$("#interno").focus();


$(".btn-levantar").click(function(event) {
	event.preventDefault();
	nro_unidad=$("#nro_unidad").val();
	id=$("#id").val();
	 swal({
	  title: "Desea Levantar el turno de Entrega?",
	  text: "Se borraran todos los datos de los mismos",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Levantar!",
	  cancelButtonText: "Cancelar",
	  closeOnConfirm: true,
	  closeOnCancel: true
	},
	function(isConfirm){
	  // if (isConfirm) {
	  //   swal("Deleted!", "Your imaginary file has been deleted.", "success");
	  // } else {
	  //   swal("Cancelled", "Your imaginary file is safe :)", "error");
	  // }
	  $(".mod").show();
		$.ajax({
			url:"entregas_agenda_turnos_levantar.php",
			cache:false,
			type:"POST",
			data:{nro_unidad, id},
			success:function(result){
				$(".mod").hide();
	 			$(".lienzo-unidad").hide();
	 			$(".contenido-principal").html(result);
			}
		});

	});
	$(".lienzo-unidad").hide();
});

$(".btn-cancelar").click(function(event) {
	event.preventDefault();
	$(".lienzo-unidad").hide();
});

$(".form-turno").submit(function(event) {
	event.preventDefault();

	if (($('#vehiculo').val()=='NO EXISTE')) {
		swal("Unidad Inexistente", "Por favor verifique los datos para reservar turno de entrega", "error");
	}else{
		$(".mod").show();
		$.ajax({
			url:"entregas_agenda_turnos_formulario_guardar.php",
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

function buscar_interno(nro_interno){
	interno=nro_interno;
	$(".mod").show();
	$.ajax({
		url:"entregas_agenda_formulario_buscar_interno.php",
		cache:false,
		type:"POST",
		data:{interno:interno},
		success:function(result){
			$(".mod").hide();
 			//$("#zona_cliente_uno").html(result);
 			var unidad = JSON.parse(result);
 			if (unidad.cantidad>0) {
	 			$("#cliente").val(unidad.cliente);
	 			$("#vehiculo").val(unidad.grupo + ' ' + unidad.modelo);
	 			$("#asesor").val(unidad.asesor);
	 			$("#nro_unidad").val(unidad.nro_unidad);
	 			if (unidad.cancelada == 1) {
	 				$("#unidad_cancelada").prop("checked", "checked");
	 			}else{
	 				$("#unidad_cancelada").prop("checked", "");
	 			}
	 		}else{
	 			$("#interno").focus();
	 			$("#interno").val('');
	 			$("#vehiculo").val('NO EXISTE');
	 			$("#cliente").val('');
	 			$("#asesor").val('');
	 			$("#nro_unidad").val('');
	 			$("#unidad_cancelada").prop("checked", "");
	 			swal("Unidad Inexistente", "Por favor verifique los datos para reservar turno de entrega", "error");
	 			
	 		}
		}
	});
}

$("#interno").focusout(function(event) {
	if ($(this).val()!='') {
		buscar_interno($(this).val());
	}
});


$("#interno").keypress(function(e) {
	
      var keycode = (event.keyCode ? event.keyCode : event.which);

	  if(keycode == '13' && $(this).val()!=''){
	  		event.preventDefault();
	  		buscar_interno($(this).val());
          // Acciones a realizar, por ej: enviar formulario.
          
       }
    });

$("#click-buscar-interno").click(function(event){
	event.preventDefault();
	if ($("#interno").val()!='') {
		buscar_interno($(this).val());
	}else{
		$("#interno").focus();
		swal("Sin Interno", "Por favor introduzca un interno para realizar la busqueda", "error");
	}
	
})