$(document).ready(function(){

	$(".mod").hide();
	$(".form_filtro").hide();

	$("#buscar").keyup(function(e){  
		var keycode = (event.keyCode ? event.keyCode : event.which);
		det=$("#buscar").val();
		cant=$("#buscar").val().length; 
		if (cant>3 || cant==0) {
			$.ajax({url:"registro_busqueda.php",cache:false,type:"POST",data:{det:det},success:function(result){
		  		$(".lista-tramites").html(result);
			}});
		}

	});

	$("#buscar").focusin(function(event) {
		$(".form_filtro").hide();
		$(".zona-activador").show();
	});

	$(".form_filtro").submit(function(event) {
		event.preventDefault();
		band=0;
		if ($("#fecha_desde").val()=='' || $("#fecha_desde").val()==null){
			band=1;
			swal("Referencias Incompletas", "Defina los parametros de 'Fecha Desde'", "error");

		}
		if ($("#fecha_hasta").val()=='' || $("#fecha_hasta").val()==null){
			band=1;
			swal("Referencias Incompletas", "Defina los parametros de 'Fecha Hasta'", "error");
		}
		if (band==0) {
			$(".mod").show();
				$.ajax({
				url:"filtro_gestoria.php",
				cache:false,
				type:"POST",
				data:$(this).serialize(),
				success:function(result){
		 			$(".contenido").html(result);
		 			$(".mod").hide();
				}
			});
		}
	});

	$(".activar-filtro").click(function(event) {
		$(".form_filtro").show(200);
		$(".zona-activador").hide();
		$("#buscar").val('');
	});









})