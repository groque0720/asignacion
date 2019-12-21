$(".cerrar-lista-check").click(function(){


	$(".mod").show();

    mes = $("#mes").val();
    ano = $("#ano").val();
    id_asesor = $("#id_asesor").val();

    $.ajax({
        url:"pmi_lista_plan_de_accion.php",
        cache:false,
        type:"POST",
        data:{mes, ano, id_asesor},
        success:function(result){
        	$("#tabla-plan-accion").html(result);
			$(".lienzo-unidad").hide();  
			$(".mod").hide();      	
        }
    });



});

$(".item").click(function(){

    id_item = $(this).attr('data-id');	

	if( $(this).prop('checked') ) {
	    valor=1;
	    $(".obs_"+id_item).removeAttr('disabled');
	}else{
		valor=0;
		$(".obs_"+id_item).val('');
		$(".obs_"+id_item).attr('disabled','disabled');
	}

    mes = $("#mes").val();
    ano = $("#ano").val();
    id_asesor = $("#id_asesor").val();

    obs = $(".obs_"+id_item).val();

        $.ajax({
            url:"lista_check_plan_de_accion_actualizar.php",
            cache:false,
            type:"POST",
            data:{mes, ano, id_asesor, id_item, valor, obs},
            success:function(result){
            }
        });

});

$(".obs").focusout(function(){

	valor=3;

    mes = $("#mes").val();
    ano = $("#ano").val();
    id_asesor = $("#id_asesor").val();
	id_item = $(this).attr('data-id');    
    obs = $(this).val();

        $.ajax({
            url:"lista_check_plan_de_accion_actualizar.php",
            cache:false,
            type:"POST",
            data:{mes, ano, id_asesor, id_item, valor, obs},
            success:function(result){

            }
        });



});