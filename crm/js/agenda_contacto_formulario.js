
$(".btn-cancelar").click(function(event) {

	event.preventDefault();
	$(".lienzo-formulario").hide();
	id_prospecto = $("#id").val();
	id_cliente = $("#id_cliente").val();
	alta_desde_prospecto = $("#alta_desde_prospecto").val();
	id_prospecto_alta = $("#id_prospecto_alta").val();

	if ($('#guardado').val()==0) {
		$.ajax({
			url:"prospecto_borrar.php",
			cache:false,
			type:"POST",
			data:{id_prospecto:id_prospecto, id_cliente:id_cliente, alta_desde_prospecto:alta_desde_prospecto, id_prospecto_alta:id_prospecto_alta},
			success:function(result){
				
			}
		});
	}

	if ($("#guardado").val()==1) {
		$.ajax({
			url:"seguimiento_limpiar.php",
			cache:false,
			type:"POST",
			data:{id_prospecto:id_prospecto},
			success:function(result){
			}
		});
	}

});

$(".form-formulario").submit(function(event) {
	event.preventDefault();
	band=0;

	// alert($("#fecha").val());


	if ($("#id_localidad").val()==0 && band==0) {
		$("#id_localidad").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Localidad' para guardar");
		band=1;
	}

	if ($("#id_asesor").val()==0 && band==0) {
		$("#id_asesor").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Asesor' para guardar");
		band=1;
	}


	if (band==0) {
		$(".mod").show();
		$.ajax({
			url:"prospecto_guardar.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			$(".lienzo-formulario").hide();
	 			$(".contenido-principal").html(result);
	 			$('.item_link').removeClass('item-activo');
				$('.item-prospecto').addClass('item-activo');
			}
		});
	}
});

$("#detalle_cliente").click(function(event){

	event.preventDefault();
	
	id= $('#id_cliente').val();
	nro=parseInt($('#lienzo_activos').val())+1
	$('#lienzo_activos').val(nro);
	id_prospecto = $("#id").val();

	$(".mod").show();
	$.ajax({
		url:'cliente_formulario.php',
		cache:false,
		type:"POST",
		data:{id:id, id_prospecto:id_prospecto},
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-formulario-"+$('#lienzo_activos').val()).html(result);
 			$(".lienzo-formulario-"+$('#lienzo_activos').val()).show();
		}
	})

});

$("#agregar_cliente").click(function(){

	event.preventDefault();
	
	nro=parseInt($('#lienzo_activos').val())+1
	$('#lienzo_activos').val(nro);

	nuevo="nuevo";
	id_prospecto = $("#id").val();

	$(".mod").show();
	$.ajax({
		url:'cliente_formulario.php',
		cache:false,
		type:"POST",
		data:{nuevo:nuevo, id_prospecto:id_prospecto},
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-formulario-"+$('#lienzo_activos').val()).html(result);
 			$(".lienzo-formulario-"+$('#lienzo_activos').val()).show();
		}
	})
});


$("#nuevo_contacto").click(function(event){
	event.preventDefault();
	// id_prospecto = $("#id").val();
	if (parseInt($("#hay_abiertos").val())>0) {
		swal("Seguimiento Abiertos!", "Por favor antes de agregar un nuevo seguimiento, cerrar el anterior..");	
	}else{

		id_prospecto= $(this).attr('data-id');
		nuevo="";
		nro=parseInt($('#lienzo_activos').val())+1
		$('#lienzo_activos').val(nro);

		$(".mod").show();
		$.ajax({
			url:'seguimiento_formulario.php',
			cache:false,
			type:"POST",
			data:{nuevo:nuevo, id_prospecto:id_prospecto},
			success:function(result){
				$(".mod").hide();
					$(".lienzo-formulario-"+$('#lienzo_activos').val()).html(result);
	 				$(".lienzo-formulario-"+$('#lienzo_activos').val()).show();
			}
		});	
	}


});

