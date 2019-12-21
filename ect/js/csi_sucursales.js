$(".definir").change(function(){

	mes=$("#mes").val();
	ano=$("#ano").val();

	if (mes!=0 && ano!='' && ano >= 2016 && ano < 2020) {

		$(".mod").show();
		$.ajax({
			url:"csi_sucursales_cuerpo.php",
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
		swal("Datos Inválidos", "Por favor verifique los datos para realizar la carga del CSI", "error");
	}
	
});

