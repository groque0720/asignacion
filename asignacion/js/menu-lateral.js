$(function() {
	var Accordion = function(el, multiple) {
		this.el = el || {};
		this.multiple = multiple || false;

		// Variables privadas
		var links = this.el.find('.link');
		// Evento
		links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
	}

	Accordion.prototype.dropdown = function(e) {
		var $el = e.data.el;
			$this = $(this),
			$next = $this.next();

		$next.slideToggle();
		$this.parent().toggleClass('open');

		if (!e.data.multiple) {
			$el.find('.submenu').not($next).slideUp().parent().removeClass('open');
		};
	}	

	var accordion = new Accordion($('#accordion'), false);
});


$('.item-menu-lateral').click(function(event) {
	$('.item-menu-lateral').removeClass('item-menu-lateral-activo');
	$(this).addClass('item-menu-lateral-activo');
	event.preventDefault();
	$('#texto_buscar').val('');
	$(".mod").show();
	modelo_activo=$(this).attr('data-id');
	$('#modelo_activo').val(modelo_activo);
	$.ajax({
		url:"carga_unidades_modelos.php",
		cache:false,
		type:"POST",
		data:{modelo_activo:modelo_activo},
		success:function(result){
			$(".mod").hide();
 			$(".contenido-principal").html(result);
		}
	});
});

setInterval(comprobarCambios,200000);

function recarga(){
	$(".mod").show();
	modelo_activo=$('#modelo_activo').val();
	$.ajax({
		url:"carga_unidades_modelos.php",
		cache:false,
		type:"POST",
		data:{modelo_activo:modelo_activo},
		success:function(result){
			$(".mod").hide();
 			$(".contenido-principal").html(result);
		}
	})
};

function comprobarCambios(){
	modelo_activo=$('#modelo_activo').val();
	nro_act=$('#nro_act').val();
	$.ajax({
		url:"a_modificaciones.php",
		cache:false,
		type:"POST",
		data:{nro_act:nro_act, modelo_activo:modelo_activo},
		success:function(result){
			$(".mod").hide();
 			// $(".contenido-principal").html(result);
 			if(result!=0){
 				act_nro=result
 				$('#nro_act').val(act_nro);
 				recarga();
 			}
		}
	})
}




$('.link').click(function(event) {
	grupo=$(this).attr('data-grupo');
	$('#grupo_activo').val(grupo);
});




$('#link_usado').click(function(event) {
	event.preventDefault();
	$(".mod").show();
	modelo_activo='';
	$.ajax({
		url:"carga_unidades_usados.php",
		cache:false,
		type:"POST",
		data:{modelo_activo:modelo_activo},
		success:function(result){
			$(".mod").hide();
 			$(".contenido-principal").html(result);
		}
	});
});