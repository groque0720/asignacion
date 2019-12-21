$(document).ready(function(){

	$("#cargar_agenda").hide();

	$( "#fecha" ).datepicker();
	$('#fecha').datepicker('setDate', 'today');

	 $.datepicker.regional['es'] = {
	 closeText: 'Cerrar',
	 prevText: '< Ant',
	 nextText: 'Sig >',
	 currentText: 'Hoy',
	 monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	 monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
	 dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
	 dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
	 dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
	 weekHeader: 'Sm',
	 dateFormat: 'dd/mm/yy',
	 firstDay: 1,
	 isRTL: false,
	 showMonthAfterYear: false,
	 yearSuffix: ''
	 };
	 $.datepicker.setDefaults($.datepicker.regional['es']);
	$(function () {
	$("#fecha").datepicker();
	});
	


	function cargarTablaAgenda(){

		if ($("#fecha").val()!='' && $("#sucursal-seleccionado").val()!='' && $("#modelo-seleccionado").val()!='' ) {
			$(".cargar_agenda").hide();
			modelo=$("#modelo-seleccionado").val();
			sucursal=$("#sucursal-seleccionado").val();
			fecha=$("#fecha").val();
			$(".mod").show();
			$.ajax({
				url:"cargar_tabla_agenda.php",
				cache:false,
				type:"POST",
				data:{modelo:modelo, sucursal:sucursal, fecha:fecha},
				success:function(result){
					$(".mod").hide();
		 			$(".agenda").html(result);
				}
			});
		}
	}

	$(".mod").hide();
	$(".lienzo-form-agendar").hide();

	$(".sucursal").click(function(event) {
		suc=$(this).attr('data-id');
		$("#sucursal-seleccionado").val(suc);
		$(".sucursal").removeClass('sucursal-activo');
		$(this).addClass('sucursal-activo');
		cargarTablaAgenda();
	});

	$(".modelo").click(function(event) {
		suc=$(this).attr('data-id');
		$("#modelo-seleccionado").val(suc);
		$(".modelo").removeClass('modelo-activo');
		$(this).addClass('modelo-activo');
		cargarTablaAgenda();
	});

	$("#fecha").change(function(event) {
  		cargarTablaAgenda();
	});//fin fecha change


	$(".cargar_agenda").click(function(event){
		event.preventDefault();
				// var result = $(this).val().split('-');
			var parts = $("#fecha").val().split("/");
		    var day = parseInt(parts[2], 10);
		    var month = parseInt(parts[1], 10);
		    var year = parseInt(parts[0], 10);

		    alert(day);
		    band=0;
			//alert( day+' - '+month+' - '+year );

			    // Revisar los rangos de año y mes
	    if( (year < 2017) || (year > 2018) || (month == 0) || (month > 12) )
	        band=1;

	    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

	    // Ajustar para los años bisiestos
	    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
	        monthLength[1] = 29;

	    // Revisar el rango del dia
	    //alert(day > 0 && day <= monthLength[month - 1]);

	    if (!(day > 0 && day <= monthLength[month - 1]) || band==1) {
	    	$("#fecha").val('');
	    	$("#fecha").focus();
	    	swal("Fecha Incorrecta", "Por Favor verifique la fecha", "error");
	    }//else{

			 //    	band=0;

		  //   	if ($("#modelo-seleccionado").val()=='') {
				// swal("Falta Datos", "Por Favor Seleccione Modelo", "error");
				// band=1;
				// }

				// if ($("#sucursal-seleccionado").val()=='') {
				// 	swal("Falta Datos", "Por Favor Seleccione Sucursal", "error");
				// 	band=1;
				// }
				// if (band==0) {

		  //   		cargarTablaAgenda();
		  //   	}
	    //}
	})



	// $("#fecha").focusout(function(){
	// 	// var result = $(this).val().split('-');
	// 	var parts = $("#fecha").val().split("-");
	//     var day = parseInt(parts[2], 10);
	//     var month = parseInt(parts[1], 10);
	//     var year = parseInt(parts[0], 10);
	//     band=0;
	// 	//alert( day+' - '+month+' - '+year );

	// 	    // Revisar los rangos de año y mes
 //    if( (year < 2017) || (year > 2018) || (month == 0) || (month > 12) )
 //        band=1;

 //    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

 //    // Ajustar para los años bisiestos
 //    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
 //        monthLength[1] = 29;

 //    // Revisar el rango del dia
 //    //alert(day > 0 && day <= monthLength[month - 1]);

 //    if (!(day > 0 && day <= monthLength[month - 1]) || band==1) {
 //    	$("#fecha").val('');
 //    	$("#fecha").focus();
 //    	swal("Fecha Incorrecta", "Por Favor verifique la fecha", "error");
 //    }else{
 //    	cargarTablaAgenda();
 //    }

	// })

});
