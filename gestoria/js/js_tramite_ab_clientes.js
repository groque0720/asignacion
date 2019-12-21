$(document).ready(function(){

	$(".remove_member").click(function(event) {
		if (parseInt($("#cant_miembros").val())>1) {
			if (confirm('Confirma la Eliminación del cliente')) {
				cant_miembros = $("#cant_miembros").val();
				id_cli=$(this).attr('data-id');
				id_reg = $('#id_reg_gestoria').val();
				$(".mod").show();
				
				$.ajax({
				url:"ajax/eliminar_persona.php",
				cache:true,
				type:"POST",
				data:{id_reg:id_reg, id_cli:id_cli, cant_miembros:cant_miembros},
				success:function(result){
					$('.zona-personas').html(result);
					cant=parseInt($("#cant_miembros").val())-1;
					$("#cant_miembros").val(cant);
					id_cli=$("#id_primer_cliente").val();
					cli=$("#nombre_primer_cliente").val();
					$('.zona-ckeck-list').html('Obteniendo Información...');
					$.ajax({
						url:"ajax/completar_zona_checklist.php",
						cache:true,
						type:"POST",
						data:{id_cli:id_cli, cli:cli},
						success:function(result){
							$('.zona-ckeck-list').html(result);
						}
					});
					
					$(".mod").hide();

				}
			});
				
			}
		}
	});


$('.cliente').click(function(event) {
	$('.zona-ckeck-list').html('Obteniendo Información....');
	$('.cliente').removeClass('cliente-seleccionado');
	$(this).addClass('cliente-seleccionado');
	id_cli=$(this).attr('data-id');
	cli=$(this).val();
	$.ajax({
		url:"ajax/completar_zona_checklist.php",
		cache:true,
		type:"POST",
		data:{id_cli:id_cli, cli:cli},
		success:function(result){
			$('.zona-ckeck-list').html(result);
		}
	});
});


$('.cliente').focusout(function(event) {
	id_cli=$(this).attr('data-id');
	cli=$(this).val();
	$.ajax({
		url:"ajax/actualizar_cliente.php",
		cache:true,
		type:"POST",
		data:{id_cli:id_cli, cli:cli},
		success:function(result){}
	});





});

$(".estado-doc").click(function(event) {
	id_cli=$(this).attr('data-id');
	estado=$(this).attr('data-estado');

	if (estado == 1) {

		swal({ 
		    title: "Estado de Documentación",
		    text: "Confirma pasar del estado Completo a Incompleto?",
		    type: "warning",
		    showCancelButton: true,
		    confirmButtonColor: "#FD2B03",
		    confirmButtonText: "Pasar a Incompleto",
		    closeOnConfirm: false },
		    function(){
		     	$(".mod").show();
			    estado=0;
				$.ajax({
					url:"ajax/validar_estado_cli.php",
					cache:true,
					type:"POST",
					data:{id_cli:id_cli, estado:estado},
					success:function(result){
						$(".mod").hide();
						$('.'+id_cli).removeClass('completo');
						$('.'+id_cli).addClass('incompleto');
						$('.'+id_cli).attr('data-estado', 0);
						swal("Documentación Incompleta", "Se pasó de Documentación Completa a Incompleta", "success");
					}
				});
		    });

	}else{

		swal({ 
		    title: "Estado de Documentación",
		    text: "Confirma pasar del estado Incompleto a Completo?",
		    type: "warning",
		    showCancelButton: true,
		    confirmButtonColor: "#26FC03",
		    confirmButtonText: "Pasar a Completo",
		    closeOnConfirm: false },
		    function(){
		     	$(".mod").show();
			    estado=1;
				$.ajax({
					url:"ajax/validar_estado_cli.php",
					cache:true,
					type:"POST",
					data:{id_cli:id_cli, estado:estado},
					success:function(result){
						$(".mod").hide();
						$('.'+id_cli).removeClass('incompleto');
						$('.'+id_cli).addClass('completo');
						$('.'+id_cli).attr('data-estado', 1);
						swal("Documentación Completa", "Se pasó de Documentación Incompleta a Completa", "success");
					}
				});
		    });






	}
});




});