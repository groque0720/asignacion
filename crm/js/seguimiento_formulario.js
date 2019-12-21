function cerrar_formulario() {
	if (parseInt($("#lienzo_activos").val())>0) {
		nro=parseInt($("#lienzo_activos").val())-1;
		$(".lienzo-formulario-"+$("#lienzo_activos").val()).hide();
		$("#lienzo_activos").val(nro);
		
	}else{
		$(".lienzo-formulario").hide();
	}
}

function cambio_en_realizado(){
	if ($("#realizado").val()==1) {
		$(".contacto_no_realizado").hide();
		$(".contacto_realizado").show();
		$("#fec_realizado").attr('disabled',false);
		$("#id_resultado").attr('disabled', false);
	}else{
		$(".contacto_no_realizado").show();
		$(".contacto_realizado").hide();
		$("#fec_realizado").attr('disabled',true);
		$("#id_resultado").attr('disabled', true);
		$("#fec_realizado").val('');
		$("#id_resultado").val(0);

	}
}

cambio_en_realizado();

$("#realizado").click(function(){
	cambio_en_realizado();
});


$(".btn-cancelar-seguimiento").click(function(event) {

	event.preventDefault();

	cerrar_formulario();

	id_seguimiento = $("#id_seguimiento").val();

	if ($('#guardado_seguimiento').val()==0) {
		$.ajax({
			url:"seguimiento_borrar.php",
			cache:false,
			type:"POST",
			data:{id_seguimiento:id_seguimiento},
			success:function(result){
			}
		});
	}

});

$("#form-seguimiento").submit(function(event) {
	event.preventDefault();
	band=0;

	// alert($("#fecha").val());

	if ($("#fec_contacto").val()==0 && band==0) {
		$("#fec_contacto").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Fecha' para guardar");
		band=1;
	}

	if ($("#id_tipo_contacto").val()==0 && band==0) {
		$("#id_tipo_contacto").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Tipo de Contacto' para guardar");
		band=1;
	}

	if ($("#realizado").val()==1) {
		if ($("#fec_realizado").val()==0 && band==0) {
			$("#fec_realizado").focus();
			swal("Campo Obligatorio!", "Por favor Ingrese 'Fecha de Realizado' para guardar");
			band=1;
		}

		if ($("#id_resultado").val()==0 && band==0) {
			$("#id_resultado").focus();
			swal("Campo Obligatorio!", "Por favor Ingrese 'Resultado' para guardar");
			band=1;
		}
	}


	if (band==0) {
		$(".mod").show();
		$.ajax({
			url:"seguimiento_guardar.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			cerrar_formulario()
	 			$("#zona_tabla_seguimiento").html(result);
			}
		});
	}
});




