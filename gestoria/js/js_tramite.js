$(document).ready(function(){

	$(".lienzo").hide();
	$(".mod").hide();

	$('#js_cargar_observacion').click(function() {
		$(".lienzo").show();
	});

	$('#cerrar_form_obs').click(function(event) {
		$(".lienzo").hide();
	});

	// $('#form_tramite').submit(function(event) {
	$('.icon-guardar').click(function(event) {
		event.preventDefault();
		band=1;

		if ($('#nro_rva').val()=='' && band==1) {
			$('#nro_rva').focus();
			swal("Campo Obligatorio", "Ingrese Nro de Reserva");
			band=0;
		}
		if ($('#sucursal').val()=='0' && band==1) {
			$('#sucursal').focus();
			swal("Campo Obligatorio", "Ingrese Sucursal de Archivo de Legajo");
			band=0;
		}
		if ($('#fec_rec_tra').val()=='' && band==1) {
			$('#fec_rec_tra').focus();
			swal("Campo Obligatorio", "Ingrese Fecha de Recepción de Legajo");
			band=0;
		}

		if ($('#asesor').val()=='0' && band==1) {
			$('#asesor').focus();
			swal("Campo Obligatorio", "Ingrese Asesor");
			band=0;
		}

		if (($('#modelo').val()=='' || $('#modelo').val()=='0') && band==1) {
			$('#modelo').focus();
			swal("Campo Obligatorio", "Ingrese Modelo");
			band=0;
		}
		if (($('#version').val()=='' || $('#version').val()=='0') && band==1) {
			$('#version').focus();
			swal("Campo Obligatorio", "Ingrese version");
			band=0;
		}

		if ($('#guardado').val()==1 && band==1) {
			$('#form_tramite').submit();
		}

		if ($('#guardado').val()==0 && band==1) {
			idsuc=$('#sucursal').val();
			if (idsuc==3 || idsuc==4) {
				suc_res = 'SP'
				idsuc=2;
			}else{
				suc_res=$('#suc_res').val();
				idsuc=$('#sucursal').val();
			}
			id_reg = $('#id_reg_gestoria').val();
			$(".mod").show();
			$.ajax({
				url:"ajax/asignar_nrolegajo.php",
				cache:true,
				type:"POST",
				data:{idsuc:idsuc, suc_res:suc_res, id_reg:id_reg},
				success:function(result){
					$('#nro_leg').val(suc_res+'-'+result);
					$(".mod").hide();
					$('#guardado').val(1);
					swal({   title: ""+suc_res+'-'+result,   text: "Número de Legajo U.I.F.: ",   imageUrl: "imagenes/logo_dyv.png" });
					setTimeout(function() {$('#form_tramite').submit();  }, 2500);
					
				}
			});
		}
		
		
	});


	$("#add_member").click(function(event) {

		swal({
		    title: "Control de Documentación U.I.F.",
		    text: "Operación Nuevo Cliente",
		    type: "input",
		    showCancelButton: true,
		    closeOnConfirm: false,
		    animation: "slide-from-top",
		    inputPlaceholder: "Nombre Completo" },

		    function(inputValue){
		        if (inputValue === false) return false;
		        if (inputValue === "") {
		        	swal.showInputError("Por favor ingrese el Nombre del CLiente!");
		        	return false
		        	}

		         

		         nvo_cli = inputValue;
			
				cant_miembros = $("#cant_miembros").val();
				id_reg = $('#id_reg_gestoria').val();
				$(".mod").show();
				$.ajax({
					url:"ajax/insertar_personas.php",
					cache:true,
					type:"POST",
					data:{nvo_cli:nvo_cli, id_reg:id_reg, cant_miembros:cant_miembros},
					success:function(result){
						$('.zona-personas').html(result);
						cant=parseInt($("#cant_miembros").val())+1;
						$("#cant_miembros").val(cant);
						swal("Se incorporó al legajo ",inputValue, "success");
						$(".mod").hide();
					}
				});

		     });
	});

	$('#modelo').change(function(event) {
		modelo=$(this).val();
		$('.mod').show();
		$.ajax({
			url:"ajax/buscar_versiones.php",
			cache:true,
			type:"POST",
			data:{modelo:modelo},
			success:function(result){
				$('#version').html(result);
				$('.mod').hide();
			}
			});
	});

	$('#add_localidad').click(function(event) {
		prov=$('#provincia').val();
		// nva_loc = prompt('Ingrese Localidad de Registro  Automotor para la provincia seleccionada');
		
		swal({
		    title: "Localidad de Registro",
		    text: "Ingrese Nombre de Localidad",
		    type: "input",
		    showCancelButton: true,
		    closeOnConfirm: false,
		    animation: "slide-from-top",
		    inputPlaceholder: "Localidad" },

		    function(inputValue){
		        if (inputValue === false) return false;
		        if (inputValue === "") {
		        	swal.showInputError("Por favor ingrese el Nombre de la Localidad!");
		        	return false
		        	}
		        nva_loc = inputValue;

				$(".mod").show();
				$.ajax({
					url:"ajax/insertar_localidad.php",
					cache:true,
					type:"POST",
					data:{prov:prov, nva_loc:nva_loc},
					success:function(result){
						$('#loc_registro').html(result);
						swal("Se incorporó nueva localidad ",inputValue, "success");
						$(".mod").hide();
						}
				});
		 	}
		 );
	});

	$('#provincia').change(function(event) {
		prov=$(this).val();
		$(".mod").show();
		$.ajax({
			url:"ajax/buscar_localidades.php",
			cache:true,
			type:"POST",
			data:{prov:prov},
			success:function(result){
				$('#loc_registro').html(result);
				$(".mod").hide();
			}
			});
	});

	$('#guardar_form_obs').click(function(event) {
		if ($('#fecha_obs').val()!='') {
			id_reg = $('#id_reg_gestoria').val();
			fecha = $('#fecha_obs').val();
			texto = $('#observacion').val();
			$(".mod").show();
			$.ajax({
				url:"ajax/insertar_observacion.php",
				cache:true,
				type:"POST",
				data:{texto:texto, fecha:fecha, id_reg:id_reg},
				success:function(result){
					$('#zona-tabla-obs').html(result);
					$('#fecha_obs').val('');
					$('#observacion').val('');
					$(".lienzo").hide();
					$(".mod").hide();
				}
			});

		}else{
			alert('Ingrese Fecha');
		};
	});




	if ($('#compra').val()==1) {
		$("#usado").hide();
	}else{
		$("#modelo").hide();
		$("#version").hide();
	}

	$('#compra').change(function() {
		if ($('#compra').val()==1) {
			$("#usado").hide();
			$("#modelo").show();
			$("#version").show();
			$("#usado").val('');
		}else{
			$("#usado").show();

			$("#modelo").hide();
			$("#version").hide();
			$('#modelo > option[value="14"]').attr('selected', 'selected');
			modelo=14;
		
			$.ajax({
				url:"ajax/buscar_versiones.php",
				cache:true,
				type:"POST",
				data:{modelo:modelo},
				success:function(result){
					$('#version').html(result);
					$('#version > option[value="100"]').attr('selected', 'selected');
				}
				});
			
			// $("#modelo").val(14);
			// $("#version").val(100);

		}
	});
	


$('.item_chech').click(function(event) {
	id_doc=$(this).attr('data-id');
	if ($(this).prop('checked')) {
		valor=1;
	}else{
		valor=0;
	};

	$.ajax({
		url:"ajax/validar_doc.php",
		cache:true,
		type:"POST",
		data:{id_doc:id_doc, valor:valor},
		success:function(result){
		}
	});

});


});

