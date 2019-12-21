$("#cargar_plan_accion").click(function(event){
	event.preventDefault();
	$(".mod").show();
	$(".lienzo-unidad").show();

    mes = $(this).attr('data-idmes');
    ano = $(this).attr('data-ano');
    id_asesor = $(this).attr('data-idasesor');

        $.ajax({
            url:"lista_check_plan_de_accion.php",
            cache:false,
            type:"POST",
            data:{mes, ano, id_asesor},
            success:function(result){
            	$(".lienzo-unidad").html(result);
            	$(".lienzo-unidad").show();
                $(".mod").hide();
            }
        });

})



$("#cargar_aspectos_mejorar").click(function(event){
    event.preventDefault();
    $(".mod").show();
    $(".lienzo-unidad").show();

    mes = $(this).attr('data-idmes');
    ano = $(this).attr('data-ano');
    id_asesor = $(this).attr('data-idasesor');

        $.ajax({
            url:"lista_check_aspectos_mejorar.php",
            cache:false,
            type:"POST",
            data:{mes, ano, id_asesor},
            success:function(result){
                $(".lienzo-unidad").html(result);
                $(".lienzo-unidad").show();
                $(".mod").hide();
            }
        });

})