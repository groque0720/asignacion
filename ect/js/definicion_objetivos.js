$(".definir").change(function(){

	mes=$("#mes").val();
	ano=$("#ano").val();
	id_tipo_objetivo=$("#objetivo").val();

	if (mes!=0 && ano!='' && ano >= 2016 && ano < 2020 && id_tipo_objetivo!=0 ) {

		$(".mod").show();
		$.ajax({
			url:"definicion_objetivos_cuerpo.php",
			cache:false,
			type:"POST",
			data:{mes, ano, id_tipo_objetivo},
			success:function(result){
				$("#zona_definicion_objetivos").html(result);
				$(".mod").hide();
			}
		});

	};

	if (ano=='' || ano < 2016 || ano > 2020) {
		swal("Datos Inválidos", "Por favor verifique los datos para realizar la definición de los Objetivos", "error");
	}
});

