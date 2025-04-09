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

$("#costo_reparacion_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#costo_reparacion_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#costo_reparacion_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#costo_reparacion").val(0);
	}else{
		$("#costo_reparacion").val(res2);
	}

});

$("#toma_mas_impuesto_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res2= res1.replace(",",".");
	$("#toma_mas_impuesto_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#toma_mas_impuesto_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#toma_mas_impuesto").val(0);
	}else{
		$("#toma_mas_impuesto").val(res2);
	}

});
//---------------------------------------------
$("#costo_contable_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#costo_contable_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#costo_contable_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#costo_contable").val(0);
	}else{
		$("#costo_contable").val(res2);
	}

});

$("#transferencia_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#transferencia_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#transferencia_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#transferencia").val(0);
	}else{
		$("#transferencia").val(res2);
	}

});

//---------------------------------------------
		// $("#precio_venta_z").focusout(function() {
		// 	valor=$(this).val();
		// 	var valor_transferencia = ($("#precio_venta").val() * 4) / 100;

		// 	if (valor=='' || valor==null) {
		// 		// ("#precio_venta").val(0);
		// 		$("#transferencia").val(0);
		// 	}else{
		// 		// $("#precio_venta").val(res2);
		// 		$("#transferencia").val(valor_transferencia);
		// 	}

		// 	$("#transferencia_z").val(formatoNumero(valor_transferencia, 2, ",", "."));

		// });
//---------------------------------------------
$("#precio_venta_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#precio_venta_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#precio_venta_z").val(formatoNumero(res2, 2, ",", "."));

	// var valor_transferencia = (res2 * 4) / 100;

	if (valor=='' || valor==null) {
		("#precio_venta").val(0);
		// $("#transferencia").val(0);
	}else{
		$("#precio_venta").val(res2);
		// $("#transferencia").val(valor_transferencia);
	}

	// $("#transferencia_z").val(formatoNumero(valor_transferencia, 2, ",", "."));

});

//---------------------------------------------
$("#precio_0km_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#precio_0km_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#precio_0km_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#precio_0km").val(0);
	}else{
		$("#precio_0km").val(res2);
	}

});


//---------------------------------------------
$("#precio_contado_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#precio_contado_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#precio_contado_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#precio_contado").val(0);
	}else{
		$("#precio_contado").val(res2);
	}

});

//---------------------------------------------
$("#precio_info_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#precio_info_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#precio_info_z").val(formatoNumero(res2, 2, ",", "."));

	if (valor=='' || valor==null) {
		("#precio_info").val(0);
	}else{
		$("#precio_info").val(res2);
	}

	var valor_transferencia = (res2 * 3.5) / 100;

	if (valor=='' || valor==null) {
		("#precio_info").val(0);
		$("#transferencia").val(0);
	}else{
		$("#precio_info").val(res2);
		$("#transferencia").val(valor_transferencia);
	}

	$("#transferencia_z").val(formatoNumero(valor_transferencia, 2, ",", "."));

});

//---------------------------------------------
$("#km_z").change(function() {
	valor=$(this).val();
	var str = valor;
	var res1= str.replace(".","");
	var res1= res1.replace(".","");
	var res2= res1.replace(",",".");
	$("#km_z").keypress(function(event) {
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			event.preventDefault();
		}
	});

	$("#km_z").val(formatoNumero(res2, 0, ",", "."));

	if (valor=='' || valor==null) {
		("#km").val(0);
	}else{
		$("#km").val(res2);
	}

});
//----------------------------------------------
$(".btn-cancelar").click(function(event) {

	event.preventDefault();
	$(".lienzo-unidad").hide();
	id_unidad = $("#id_unidad").val();

	if ($('#guardado').val()==0) {
		$.ajax({
			url:"unidad_borrar_usado.php",
			cache:false,
			type:"POST",
			data:{id_unidad:id_unidad},
			success:function(result){
			}
		});
	}

});


$("#levantar_reserva").click(function(event) {

alert('Se limpiaran los campos de la reserva del asesor');
	$("#id_asesor > option[value=1]").attr('selected', 'selected');
	$("#estado_reserva > option[value=0]").attr('selected', 'selected');
	$("#cliente").val('');
	$("#fec_reserva").val('');
	//$("#fec_limite").val('');
	$("#hora").val('');



});







//--------------------------------------------

if ($('#id_perfil').val()==3) {

	if ($('#reservada').val()==1) {
		$('#cliente').attr('readonly', 'readonly');
	}else{
		asesor_a_reservar=$('#asesor_a_reservar').val();
		$("#id_asesor > option[value="+asesor_a_reservar+"]").attr('selected', 'selected');
	}

	$('#id_asesor option:not(:selected)').attr('disabled',true);
	$('#asesortoma option:not(:selected)').attr('disabled',true);
	// $('#id_negocio option:not(:selected)').attr('disabled',true);
	// $('#id_mes option:not(:selected)').attr('disabled',true);
	// $('#grupo option:not(:selected)').attr('disabled',true);
	// $('#id_modelo option:not(:selected)').attr('disabled',true);
	// $('#id_color option:not(:selected)').attr('disabled',true);
	// $('#id_ubicacion option:not(:selected)').attr('disabled',true);
	// $('#estado_tasa option:not(:selected)').attr('disabled',true);
	$('#estado_reserva option:not(:selected)').attr('disabled',true);

}



$(".form-unidad").submit(function(event) {
	event.preventDefault();
	$band=0;

	// if ($("#id_mes").val()==0 && $band==0) {
	// 	$("#id_mes").focus();
	// 	swal("Campo Obligatorio!", "Por favor Ingrese 'Mes' para guardar esta unidad");
	// 	$band=1;
	// }

	// if ($("#año").val()==0 && $band==0) {
	// 	$("#año").focus();
	// 	swal("Campo Obligatorio!", "Por favor Ingrese 'Año' para guardar esta unidad");
	// 	$band=1;
	// }

	// if ($("#grupo").val()==0 && $band==0) {
	// 	$("#grupo").focus();
	// 	swal("Campo Obligatorio!", "Por favor Ingrese 'Modelo' para guardar esta unidad");
	// 	$band=1;
	// }

	// if ($("#id_modelo").val()==0 && $band==0) {
	// 		$("#id_modelo").focus();
	// 		swal("Campo Obligatorio!", "Por favor Ingrese 'Versión' para guardar esta unidad");
	// 		$band=1;
	// 	}


// if ($('#id_perfil').val()==3) { //consulto si es asesor tiene que consignar estos datos obligatoriamente

// 	if ($('#cliente').val()=='' && $band==0) {
// 		$band=1;
// 		$('#cliente').focus();
// 		swal("Campo Obligatorio!", "Por favor Ingrese 'Cliente' para continuar");
// 	}

// 	if ($('#id_sucursal').val()==0 && $band==0) {
// 		$band=1;
// 		$('#id_sucursal').focus();
// 		swal("Campo Obligatorio!", "Por favor Ingrese 'Sucursal de Destino' para continuar");
// 	}

// 	if ($('#color_uno').val()==0 && $band==0) {
// 		$band=1;
// 		$('#color_uno').focus();
// 		swal("Campo Obligatorio!", "Por favor Ingrese 'Color Uno' para continuar");
// 	}

// 	if ($('#color_dos').val()==0 && $band==0) {
// 		$band=1;
// 		$('#color_dos').focus();
// 		swal("Campo Obligatorio!", "Por favor Ingrese 'Color Dos' para continuar");
// 	}

// 	if ($('#color_tres').val()==0 && $band==0) {
// 		$band=1;
// 		$('#color_tres').focus();
// 		swal("Campo Obligatorio!", "Por favor Ingrese 'Color Tres' para continuar");
// 	}

// }

	if ($band==0) {
		$(".mod").show();
		
		// Guardar los valores originales antes de enviar
		var originalPrecio = $("#precio_venta").val();
		var dominio = $("#dominio").val().trim().toUpperCase();
		var interno = $("#interno").val().trim();
		var vehiculo = $("#vehiculo").val().trim(); //ver
		var año = $("#año").val()
		var km = $("#km").val()
		var color = $("#id_color").val()
		var id_estado_certificado = $("#id_estado_certificado").val()
		var estado_reserva = $("#estado_reserva").val()
		var id_estado = $("#id_estado").val()

		//Ahora guardo el estado de la reserva a "entregado" solo si la fecha de entrega está establecida
		if ($('#fec_entrega').val() != '') {
			var entregado = 1;
		} else {
			var entregado = 0;
		}
		
		//Guardado
		//var $guardado = $('#guardado').val();

		$.ajax({
			url:"guardar_usado.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
				$(".mod").hide();
	 			$(".lienzo-unidad").hide();
	 			$(".contenido-principal").html(result);
				
				// Crear un iframe oculto para hacer la petición de forma independiente
				if (originalPrecio) { //
					enviarDatosAPI(dominio, interno, originalPrecio, vehiculo, año, km, color, id_estado_certificado, estado_reserva, entregado, id_estado);
				}
			}
		});
	}
});

// Función simple para enviar datos a la API
function enviarDatosAPI(dominio, interno, precio, vehiculo, año, km, color, id_estado_certificado, estado_reserva, entregado, id_estado) {
	// Crear un form oculto y enviarlo a un PHP separado que manejará la comunicación con la API
	var form = document.createElement('form');
	form.method = 'POST';
	form.action = 'enviar_api_usados.php';
	form.style.display = 'none';
	
	// Agregar los campos necesarios
	var dominioInput = document.createElement('input');
	dominioInput.type = 'hidden';
	dominioInput.name = 'dominio';
	dominioInput.value = dominio;
	form.appendChild(dominioInput);
	
	var internoInput = document.createElement('input');
	internoInput.type = 'hidden';
	internoInput.name = 'interno';
	internoInput.value = interno;
	form.appendChild(internoInput);
	
	var precioInput = document.createElement('input');
	precioInput.type = 'hidden';
	precioInput.name = 'precio_venta';
	precioInput.value = precio;
	form.appendChild(precioInput);

	var vehiculoInput = document.createElement('input');
	vehiculoInput.type = 'hidden';
	vehiculoInput.name = 'vehiculo';
	vehiculoInput.value = vehiculo;
	form.appendChild(vehiculoInput);

	var añoInput = document.createElement('input');
	añoInput.type = 'hidden';
	añoInput.name = 'año';
	añoInput.value = año;
	form.appendChild(añoInput);

	var kmInput = document.createElement('input');
	kmInput.type = 'hidden';
	kmInput.name = 'km';
	kmInput.value = km;
	form.appendChild(kmInput);

	var colorInput = document.createElement('input');
	colorInput.type = 'hidden';
	colorInput.name = 'color';
	colorInput.value = color;
	form.appendChild(colorInput);

	var id_estado_certificadoInput = document.createElement('input');
	id_estado_certificadoInput.type = 'hidden';
	id_estado_certificadoInput.name = 'id_estado_certificado';
	id_estado_certificadoInput.value = id_estado_certificado;
	form.appendChild(id_estado_certificadoInput);

	var estado_reservaInput = document.createElement('input');
	estado_reservaInput.type = 'hidden';
	estado_reservaInput.name = 'estado_reserva';
	estado_reservaInput.value = estado_reserva;
	form.appendChild(estado_reservaInput);

	var entregadoInput = document.createElement('input');
	entregadoInput.type = 'hidden';
	entregadoInput.name = 'entregado';
	entregadoInput.value = entregado;
	form.appendChild(entregadoInput);

	var id_estadoInput = document.createElement('input');
	id_estadoInput.type = 'hidden';
	id_estadoInput.name = 'id_estado';
	id_estadoInput.value = id_estado;
	form.appendChild(id_estadoInput);

	
	// Agregar el formulario al documento y enviarlo
	document.body.appendChild(form);
	form.submit();
	
	console.log("Enviando datos a API:", dominio, interno, precio);
}
