/**
 * Da formato a un número para su visualización
 *
 * @param {(number|string)} numero Número que se mostrará
 * @param {number} [decimales=null] Nº de decimales (por defecto, auto); admite valores negativos
 * @param {string} [separadorDecimal=","] Separador decimal
 * @param {string} [separadorMiles=""] Separador de miles
 * @returns {string} Número formateado o cadena vacía si no es un número
 *
 * @version 2014-07-18
 */
	function formatoNumero(numero, decimales, separadorDecimal, separadorMiles) {
	    var partes, array;

	    if ( !isFinite(numero) || isNaN(numero = parseFloat(numero)) ) {
	        return "";
	    }
	    if (typeof separadorDecimal==="undefined") {
	        separadorDecimal = ",";
	    }
	    if (typeof separadorMiles==="undefined") {
	        separadorMiles = "";
	    }

	    // Redondeamos
	    if ( !isNaN(parseInt(decimales)) ) {
	        if (decimales >= 0) {
	            numero = numero.toFixed(decimales);
	        } else {
	            numero = (
	                Math.round(numero / Math.pow(10, Math.abs(decimales))) * Math.pow(10, Math.abs(decimales))
	            ).toFixed();
	        }
	    } else {
	        numero = numero.toString();
	    }

	    // Damos formato
	    partes = numero.split(".", 2);
	    array = partes[0].split("");
	    for (var i=array.length-3; i>0 && array[i-1]!=="-"; i-=3) {
	        array.splice(i, 0, separadorMiles);
	    }
	    numero = array.join("");

	    if (partes.length>1) {
	        numero += separadorDecimal + partes[1];
	    }

	    return numero;
	}

$('#text_busqueda').val($('#texto_buscar').val());
$("#costo_z").val(formatoNumero($("#costo").val(), 2, ",", "."));
$('#es_planilla_tpa').val($('#es_tpa').val());
$('#es_planilla_entregas').val($('#es_entrega').val());

$(".btn-cancelar").click(function(event) {

	event.preventDefault();
	$(".lienzo-unidad").hide();
	id_unidad = $("#id_unidad").val();

	if ($('#guardado').val()==0) {
		$.ajax({
			url:"unidad_borrar.php",
			cache:false,
			type:"POST",
			data:{id_unidad:id_unidad},
			success:function(result){
			}
		});
	}

});

$("#costo_z").focusout(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res2= res1.replace(",",".");
	$("#costo_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#costo_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#costo").val(0);
	}else{
		$("#costo").val(res2);
	}

});

$(".form-unidad").submit(function(event) {
	event.preventDefault();


	//cargo automaticamente la fecha limite de cancelacion si tiene fecha de reserva y fecha de llegada
	if ($('#fec_reserva').val()!='' && $('#fec_reserva').val()!=null && $('#fec_arribo').val()!='' && $('#fec_arribo').val()!=null) {

		if ($('#fec_reserva').val()>=$('#fec_arribo').val()) {
			$fecha=$('#fec_reserva').val().split('-');
		}else{
			$fecha=$('#fec_arribo').val().split('-');
		}

		$ano=parseInt($fecha[0]);
		$mes=parseInt($fecha[1]);
		$dia=parseInt($fecha[2])+5;


		if ($dia>30) { $dia=parseInt($dia)-30;  $mes++;}
		if (parseInt($dia)<10) { $dia='0'+parseInt($dia);}

		if ($mes>12) { $mes=parseInt($mes)-12; $ano++;}
		if (parseInt($mes)<10) { $mes='0'+parseInt($mes);}

		$fecha_nueva= $dia+'-'+$mes+'-'+$ano;
		$fecha_nueva = $ano +'-'+$mes+'-'+$dia;

		$("#fec_limite").val($fecha_nueva);

	}else{
		$("#fec_limite").val('');
	}

	$band=0;

	if ($("#id_mes").val()==0 && $band==0) {
		$("#id_mes").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Mes' para guardar esta unidad");
		$band=1;
	}

	if ($("#año").val()==0 && $band==0) {
		$("#año").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Año' para guardar esta unidad");
		$band=1;
	}

	if ($("#grupo").val()==0 && $band==0) {
		$("#grupo").focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Modelo' para guardar esta unidad");
		$band=1;
	}

	if ($("#id_modelo").val()==0 && $band==0) {
			$("#id_modelo").focus();
			swal("Campo Obligatorio!", "Por favor Ingrese 'Versión' para guardar esta unidad");
			$band=1;
		}


if ($('#id_perfil').val()==3) { //consulto si es asesor tiene que consignar estos datos obligatoriamente

	if ($('#cliente').val()=='' && $band==0) {
		$band=1;
		$('#cliente').focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Cliente' para continuar");
	}

	if ($('#id_sucursal').val()==0 && $band==0) {
		$band=1;
		$('#id_sucursal').focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Sucursal de Destino' para continuar");
	}

	if ($('#color_uno').val()==0 && $band==0) {
		$band=1;
		$('#color_uno').focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Color Uno' para continuar");
	}

	if ($('#color_dos').val()==0 && $band==0) {
		$band=1;
		$('#color_dos').focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Color Dos' para continuar");
	}

	if ($('#color_tres').val()==0 && $band==0) {
		$band=1;
		$('#color_tres').focus();
		swal("Campo Obligatorio!", "Por favor Ingrese 'Color Tres' para continuar");
	}

}

	if ($('#cliente').val().length<3 && $('#fec_reserva').val()!='' && $('#fec_reserva').val()!=null) {
		$band=1;
		$('#cliente').focus();
		swal("Nombre de Cliente Inválido!", "Por favor Ingrese un 'Nombre Válido' para continuar");
	}

	if ($band==0) {
		$(".mod").show();
		$.ajax({
			url:"guardar_unidad.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			$(".lienzo-unidad").hide();
	 			$(".contenido-principal").html(result);
			}
		});
	}


});

$("#grupo").change(function(event) {
	grupo=$(this).val();
	$(".mod").show();
	$.ajax({
		url:"buscar_modelos.php",
		cache:false,
		type:"POST",
		data:{grupo:grupo},
		success:function(result){
				$(".mod").hide();
				$("#id_modelo").html(result);
			}
	});
});



//cuando abre el formulario de la unidad si no esta reservado por nadie tiene que cargar el vendedor que esta activo.

if ($('#id_perfil').val()==3) {

	if ($('#reservada').val()==1) {
		$('#cliente').attr('readonly', 'readonly');
			$('#color_uno option:not(:selected)').attr('disabled',true);
			$('#color_dos option:not(:selected)').attr('disabled',true);
			$('#color_tres option:not(:selected)').attr('disabled',true);
			$('#id_sucursal option:not(:selected)').attr('disabled',true);
	}else{
		asesor_a_reservar=$('#asesor_a_reservar').val();
		$("#id_asesor > option[value="+asesor_a_reservar+"]").attr('selected', 'selected');
		suc_a_reservar=$('#suc_a_reservar').val();
		$("#id_sucursal > option[value="+suc_a_reservar+"]").attr('selected', 'selected');

	}

	$('#id_asesor option:not(:selected)').attr('disabled',true);
	$('#id_negocio option:not(:selected)').attr('disabled',true);
	$('#id_mes option:not(:selected)').attr('disabled',true);
	$('#grupo option:not(:selected)').attr('disabled',true);
	$('#id_modelo option:not(:selected)').attr('disabled',true);
	$('#id_color option:not(:selected)').attr('disabled',true);
	$('#id_ubicacion option:not(:selected)').attr('disabled',true);
	$('#estado_tasa option:not(:selected)').attr('disabled',true);
	$('#estado_reserva option:not(:selected)').attr('disabled',true);
	$('#id_sucursal option:not(:selected)').attr('disabled',true);

}
//script para que los chicos de entrega no puedan cambiar el destino de las unidades.
// if ($('#id_perfil').val()==5) {
// 	$('#id_sucursal option:not(:selected)').attr('disabled',true);
// }




$("#levantar_reserva").click(function(event) {
	event.preventDefault();
	swal({
	  title: "Levantar Reserva",
	  text: "Confirma borrar todos los datos de la reserva?",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Si, Levantar!",
	  closeOnConfirm: true },
	  function(){
		$("#id_asesor > option[value=1]").attr('selected', 'selected');
		$("#color_uno > option[value=0]").attr('selected', 'selected');
		$("#color_dos > option[value=0]").attr('selected', 'selected');
		$("#color_tres > option[value=0]").attr('selected', 'selected');
		$("#id_sucursal > option[value=0]").attr('selected', 'selected');
		$("#estado_reserva > option[value=0]").attr('selected', 'selected');
		$("#cliente").val('-');
		$("#fec_reserva").val('');
		$("#fec_limite").val('');
		$("#hora").val('00:00');
	 });
});



$("#fec_arribo").focusout(function(event) {
	//cuando actualizo fecha de arribo sumo diez dias como fecha limite de cancelacion

	if ($('#reservada').val()==1) {

		$fecha=$(this).val().split('-');
		$ano=parseInt($fecha[0]);
		$mes=parseInt($fecha[1]);
		$dia=parseInt($fecha[2])+5;


		if ($dia>30) { $dia=parseInt($dia)-30;  $mes++;}
		if (parseInt($dia)<10) { $dia='0'+parseInt($dia);}

		if ($mes>12) { $mes=parseInt($mes)-12; $ano++;}
		if (parseInt($mes)<10) { $mes='0'+parseInt($mes);}

		$fecha_nueva= $dia+'-'+$mes+'-'+$ano;
		$fecha_nueva = $ano +'-'+$mes+'-'+$dia;

		$("#fec_limite").val($fecha_nueva);

	}

});

$("#fec_reserva").focusout(function(event) {
		//cuando actualizo fecha de arribo sumo diez dias como fecha limite de cancelacion

	if ($('#fec_arribo').val()!='' && $('#fec_arribo').val()!=null) {

		$fecha=$(this).val().split('-');
		$ano=parseInt($fecha[0]);
		$mes=parseInt($fecha[1]);
		$dia=parseInt($fecha[2])+5;


		if ($dia>30) { $dia=parseInt($dia)-30;  $mes++;}
		if (parseInt($dia)<10) { $dia='0'+parseInt($dia);}

		if ($mes>12) { $mes=parseInt($mes)-12; $ano++;}
		if (parseInt($mes)<10) { $mes='0'+parseInt($mes);}

		$fecha_nueva= $dia+'-'+$mes+'-'+$ano;
		$fecha_nueva = $ano +'-'+$mes+'-'+$dia;

		$("#fec_limite").val($fecha_nueva);

	}

});


$("#fec_entrega").focusin(function(event) {

	if ($("#fec_cancelacion").val()=='' || $("#fec_cancelacion").val()=='') {
		swal("ALERTA!", "No se puede Entregar Unidad Sin que este Cancelada la misma");
		$('#fec_entrega').val('');
		$('#nro_remito').val('');
		$('#fec_entrega').attr('disabled',true);
		$('#nro_remito').attr('disabled',true);
	}

});

$("#nro_remito").focusin(function(event) {

	if ($("#fec_cancelacion").val()=='' || $("#fec_cancelacion").val()=='') {
		swal("ALERTA!", "No se puede Entregar Unidad Sin que este Cancelada la misma");
		$('#fec_entrega').val('');
		$('#nro_remito').val('');
		$('#fec_entrega').attr('disabled',true);
		$('#nro_remito').attr('disabled',true);
	}

});


















// $(".btn-reservar").click(function(event) {
// 	event.preventDefault();
// 	if ($("#hora").val()=='') {
// 		var tiempo = new Date();
// 		var hora = tiempo.getHours();
// 		var minuto = tiempo.getMinutes();
// 		var segundo = tiempo.getSeconds();

// 		$("#hora").val(hora+':'+minuto+':'+segundo);

// 		var day = ("0" + tiempo.getDate()).slice(-2);
// 		var month = ("0" + (tiempo.getMonth() + 1)).slice(-2);
// 		var today = tiempo.getFullYear()+"-"+(month)+"-"+(day) ;

// 		$('#fec_reserva').val(today);
// 		var id_usuario = $("#id_usuario").val();
// 		$('#id_asesor option[value='+id_usuario+']').attr('selected','selected');
// 	}
// });

























// $(".btn-reservar").click(function(event) {
// 	event.preventDefault();
// 	if ($("#hora").val()=='') {
// 		var tiempo = new Date();
// 		var hora = tiempo.getHours();
// 		var minuto = tiempo.getMinutes();
// 		var segundo = tiempo.getSeconds();

// 		$("#hora").val(hora+':'+minuto+':'+segundo);

// 		var day = ("0" + tiempo.getDate()).slice(-2);
// 		var month = ("0" + (tiempo.getMonth() + 1)).slice(-2);
// 		var today = tiempo.getFullYear()+"-"+(month)+"-"+(day) ;

// 		$('#fec_reserva').val(today);
// 		var id_usuario = $("#id_usuario").val();
// 		$('#id_asesor option[value='+id_usuario+']').attr('selected','selected');
// 	}
// });

