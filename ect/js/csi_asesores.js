$(".definir").change(function(){

	mes=$("#mes").val();
	ano=$("#ano").val();
	// id_tipo_objetivo=$("#objetivo").val();

	if (mes!=0 && ano!='' && ano >= 2016 && ano < 2020 ) {

		$(".mod").show();
		$.ajax({
			url:"csi_asesores_cuerpo.php",
			cache:false,
			type:"POST",
			data:{mes, ano},
			success:function(result){
				$("#zona_definicion_objetivos").html(result);
				$(".mod").hide();
			}
		});

	};

	if (ano=='' || ano < 2016 || ano > 2020) {
		swal("Datos Inválidos", "Por favor verifique los datos para realizar la carga de cumplimientos", "error");
	}
});


$("#detalle_csi_asesor").click(function(event){
	event.preventDefault();
	$("#tabla_csi_asesores").hide();
})

