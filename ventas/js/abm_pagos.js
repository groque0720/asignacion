$(document).ready(function(){

 		$("#cargaestado").hide();

//------------------------------------------------------------------------------------------------------
	$(".eliminar_f").click(function(){
		if (confirm("Seguro que deseas anular??")) {
		
			$("#movimiento").val(3);

			id = $(this).attr('data-id');
		  	movi=$("#movimiento").val();
		  	fec=$("#fecha").val();
		    tipo=$("#tipo_pago").val();
		    modo=$("#modo_pago").val();
		    nrorec=$("#nrorecibo").val();
		    monto=$("#monto_pago").val();
		    obse=$("#observacion").val();
		    idres=$("#nroreserva").val();
		    fi=$("#financiera").val();
		    tot=$("#montooperacion").val();

		  	

		  	$.ajax({
		  			url:"pagos_insertar_filas.php",
			    	cache:false,
			    	type:"POST",
			    	data:{
			    			nrolin:id,
			    			mov:movi,
			    			total:tot,
				    			idreserva:idres,
				    			fecha:fec,
				    			tipo_pago:tipo,
				    			modo_pago:modo,
				    			nrorecibo:nrorec,
				    			monto_pago:monto,
				    			finan:fi,
				    			obs:obse
			    			},
				    success:function(result){
		      					$("#act_ajax").html(result);
	  						}
					});
		}
	});
//---------------------------------------------------------------------------------------------------

 		if ($("#idperfil").val()==8 | $("#idusu").val()== 119 || $("#idusu").val()== 120 || $("#idusu").val()==87 || $("#idusu").val()==28 || $("#idusu").val()==11 || $("#idusu").val()==94 || $("#idusu").val()==96) {
 			$("#cargaestado").show();
 			$("#nocredito").hide();
 		}else{
 			$("#nocredito").show();
 			$("table tr td:last-child").css({ 
   				"display": "none"
				}); 
	 		};
//---------------------------------------------------------------------------------------------------

	  	$("#boton").click(function(){
	  		$("#movimiento").val(1);

			$("#monto_pago").val(0);
			$("#observacion").val('');
			$("#nrorecibo").val('');
			$("#fecha").val('');
			$("#modo_pago").val('');
			$("#tipo_pago").val('');
			$("#financiera").val('');
		  	$( "#dialogo" ).dialog("open");
	  	 });
//-----------------------------------------------------------------------------------------	  	

  	$(".editar_f").click(function(){
		id = $(this).attr('data-id');
		
	  	$("#movimiento").val(2);

	  	$('#tabla_p tr').eq(id).each(function () {
	  		var celda = 1;
            $(this).find('td').each(function () {
            	if (celda == 1) {$("#nroli").val($(this).html());};
            	if (celda == 2) {
            						var fecha = $(this).html();
            						var fecha_a = fecha.split("-");
            						var fecha_r = String(fecha_a[2].substring(0,4)+'-'+fecha_a[1]+'-'+fecha_a[0]);
            						$("#fecha").val(fecha_r);
								};


            		// $("#fecha").val($(this).html());};
            	if (celda == 3) { 
            					abuscar=$(this).html();
	            					$("#tipo_pago option").each(function(){
		            					if ($(this).text()==abuscar) {
		            						$(this).attr("selected",true);
		            					};
	   			
									});
	            				};
            	if (celda == 4) {
            					abuscar=$(this).html();
            						$("#modo_pago option").each(function(){
		            					if ($(this).text()==abuscar) {
		            						$(this).attr("selected",true);
		            					};
	   			
									});
            					};
            	if (celda == 5) {
            					abuscar=$(this).html();
            						$("#financiera option").each(function(){
		            					if ($(this).text()==abuscar) {
		            						$(this).attr("selected",true);
		            					};
	   			
									});
            					};
            	if (celda == 6) {$("#nrorecibo").val($(this).html());};
            	if (celda == 7) { texto=$(this).html();
            						texto=texto.replace(".","");
            						texto=texto.replace(",",".");
            						$("#monto_pago").val(texto);}
            	if (celda == 8) {$("#observacion").val($(this).html());};
            	
                celda++;
            });
        });

	  	$( "#dialogo" ).dialog("open");
  	});
//-------------------------------------------------------------------------------------------------

	  	$( "#dialogo" ).dialog({
     	autoOpen: false, // no abrir automáticamente
     	resizable: true, //permite cambiar el tamaño
     	width: 600,
     	height:440, // altura
	    modal: true, //capa principal, fondo opaco
	    buttons: { //crear botón de cerrar
	    "Confirmar": function() { 
		    //inicio CONFIRMAR PAGO
    		if (($("#fecha").val() == 0)||($("#tipo_pago").val() == 0)||($("#modo_pago").val() == 0)||($("#monto_pago").val() == 0) ) { 
 				alert("Ingrese como minimo los datos necesarios..");
 			}else{

 				if ((($("#modo_pago").val() == 3 || $("#modo_pago").val() == 4) && ($("#financiera").val() != 0)) || ($("#modo_pago").val() != 3 && $("#modo_pago").val() != 4)) {


	 				$( this ).dialog( "close" );
	 				
	 				nroli=$("#nroli").val();
	 				movi=$("#movimiento").val();	
					fec=$("#fecha").val();
				    tipo=$("#tipo_pago").val();
				    modo=$("#modo_pago").val();
				    nrorec=$("#nrorecibo").val();
				    monto=$("#monto_pago").val();
				    obse=$("#observacion").val();
				    idres=$("#nroreserva").val();
				    fi=$("#financiera").val();
				    tot=$("#montooperacion").val();
				    $.ajax({url:"pagos_insertar_filas.php",
			    		cache:false,type:"POST",
			    		data:{
			    				nrolin:nroli,
			    				mov:movi,
				    			total:tot,
				    			idreserva:idres,
				    			fecha:fec,
				    			tipo_pago:tipo,
				    			modo_pago:modo,
				    			nrorecibo:nrorec,
				    			monto_pago:monto,
				    			finan:fi,
				    			obs:obse},
				    			success:function(result){
		      							$("#act_ajax").html(result);
	  						}
						});
					}else{
						alert("Si es Financiado o Leasing ingresar Fianciera","Registro de Pagos")};
	 			
	 			};		
	   		},
        "Cancelar": function() {
          $( this ).dialog( "close" );
        	}
          }
	    });


 	});