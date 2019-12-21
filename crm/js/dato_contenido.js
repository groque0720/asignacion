$(".celda").click(function(){

	id= $(this).attr('data-id');

	$(".mod").show();
	$.ajax({
		url:'dato_formulario.php',
		cache:false,
		type:"POST",
		data:{id:id},
		success:function(result){
			$(".mod").hide();
 			$(".lienzo-formulario").html(result);
  		$(".lienzo-formulario").show();
		}
	})
});

$("#realizar_prospecto").click(function(event){


	id_form_cliente=$(this).attr('data-id');
	nombre_cliente=$(this).attr('data-nombre');
	telefono_cliente=$(this).attr('data-tel');;
	//Fin de actualizacion de formulario de prospecto

	swal({
	  title: "Nuevo Prospecto",
	  text: "Desea Generar ahora un Prospecto del dato cargado?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  cancelButtonText: "No, Después",
	  confirmButtonText: "Si, Generar",
	  closeOnConfirm: true
	},
		function(){
			$(".mod").show();
			$.ajax({
				url: 'prospecto_formulario.php',
				cache:false,
				type:"POST",
				data:{nuevo:nuevo},
				success:function(result){
				$(".mod").hide();
	 			$(".lienzo-formulario").html(result);
	  			$(".lienzo-formulario").show();
	  			$("#id_cliente").val(id_form_cliente);
				$("#nombre_cliente").val(nombre_cliente);
				$("#telefono_cliente").val(telefono_cliente);
				$("#agregar_cliente").hide();
	    		}
	    	});
	});

});