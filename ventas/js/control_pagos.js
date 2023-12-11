$(document).ready(function(){

	$("#carga").hide();

	$("#sucursal").val();
	$("#estado").val();


	if ($("#perfil").val()!=9) {

		$("table tr td:last-child").css({
	   	"display": "none"
		});
	};
//----------------------------------------------------------------------------------------

	$( "#form" ).dialog({
     	autoOpen: false, // no abrir automáticamente
     	resizable: true, //permite cambiar el tamaño
     	width: 450,
     	height:440, // altura
	    modal: true, //capa principal, fondo opaco
	    buttons: { //crear botón de cerrar
	    "Confirmar": function() {

	    	fila=$("#nrofila").val()-1;
	    	nrou=$("#nrounidad").val();

	    	if ( nrou==0 || nrou=='' || parseInt(nrou) < 300 ) {

	    		alert('Por favor Buscar el número de unidad correspondiente en la Planilla de Asignación');

	    	}else{

		    	$($('#tabla_p').find('tbody > tr')[fila]).children('td')[1].innerHTML = $("#nrounidad").val();
				$($('#tabla_p').find('tbody > tr')[fila]).children('td')[2].innerHTML = $("#interno").val();
		    	$($('#tabla_p').find('tbody > tr')[fila]).children('td')[3].innerHTML = $("#nroorden").val();

		    	if ($("#arribo").val() !=null && $("#arribo").val()!="") {
			    	var fecha = $("#arribo").val();
					var fecha_a = fecha.split("-");
					var fecha_r = String(fecha_a[2].substring(0,4)+'-'+fecha_a[1]+'-'+fecha_a[0]);
					$($('#tabla_p').find('tbody > tr')[fila]).children('td')[13].innerHTML = fecha_r;
				}else{
					$($('#tabla_p').find('tbody > tr')[fila]).children('td')[13].innerHTML = " ";
				};

				if ($("#cancela").val()!=null && $("#cancela").val()!="") {
					var fecha = $("#cancela").val();
					var fecha_a = fecha.split("-");
					var fecha_r = String(fecha_a[2].substring(0,4)+'-'+fecha_a[1]+'-'+fecha_a[0]);

			    	$($('#tabla_p').find('tbody > tr')[fila]).children('td')[14].innerHTML = fecha_r;
				}else{
					$($('#tabla_p').find('tbody > tr')[fila]).children('td')[14].innerHTML = " ";
				};

		    	$($('#tabla_p').find('tbody > tr')[fila]).children('td')[15].innerHTML = $("#obs").val();
		    	idres = $("#idreserva").val();
		    	nroint = $("#interno").val();
		    	if ($("#arribo").val()!=null && $("#arribo").val()!="" && $("#arribo").val()!=0) {fecarr = $("#arribo").val();}else{fecarr = null;};
		    	if ($("#cancela").val()!=null && $("#cancela").val()!="" && $("#cancela").val()!=0) {feccan = $("#cancela").val();}else{feccan = null;};

		    	obs = $("#obs").val();
		    	fecent = $("#entrega").val();
		    	nroorden = $("#nroorden").val();


		    	$.ajax({url:"control_pagos_clientes_edit.php",cache:false,type:"POST",data:{nrou:nrou, id:idres, nroint:nroint, fecarr:fecarr, feccan:feccan, obs:obs, fecent:fecent, no:nroorden},success:function(result){

		    	}});
		    	$( this ).dialog( "close" );
	    	}

	   		},
        "Cancelar": function() {
          $( this ).dialog( "close" );
        	}
          }
	    });

	//---------------------------------------------------------------------------------------------------

	$("#sucursal").change(function(){
		$("#carga").show();
		$("#texto_busqueda").val('');

		var myDate = new Date();
		var displayDate = (myDate.getDate()) + '-' + (myDate.getMonth()+1) + '-' + myDate.getFullYear();

		suc=$("#sucursal option:selected").text();
		estado=$("#estado option:selected").text()
		leyenda = "<h1> Sucursal: <strong style='font-style:italic;'>"+suc+"</strong> - Estado: <strong style='font-style: italic;'>"+estado+"</strong>  - Fecha: <strong style='font-style: italic;'>"+displayDate+"</strong> </h1> ";
		$("#titulo").html(leyenda);

		est=$("#estado").val();
		idsuc=$("#sucursal").val();
		$.ajax({url:"control_pagos_cliente_filtro.php",cache:false,type:"POST",data:{id:idsuc, est:est},success:function(result){
	      	$("#actualizar").html(result);
	      	$("#carga").hide();

	    	}});
	});

	//-----------------------------------------------------------------------------------------------------

		$("#estado").change(function(){
		$("#carga").show();
		$("#texto_busqueda").val('');

		var myDate = new Date();
		var displayDate = (myDate.getDate()) + '-' + (myDate.getMonth()+1) + '-' + myDate.getFullYear();

		suc=$("#sucursal option:selected").text();
		estado=$("#estado option:selected").text()
		leyenda = "<h1> Sucursal: <strong style='font-style:italic;'>"+suc+"</strong> - Estado: <strong style='font-style: italic;'>"+estado+"</strong>  - Fecha: <strong style='font-style: italic;'>"+displayDate+"</strong> </h1> ";
		$("#titulo").html(leyenda);


		idsuc=$("#sucursal").val();
		est=$("#estado").val();
		tipo_venta=$("#tipo_venta").val();

		$.ajax({url:"control_pagos_cliente_filtro.php",cache:false,type:"POST",data:{id:idsuc, est:est, tipo_venta:tipo_venta},success:function(result){
	      	$("#actualizar").html(result);
	      	$("#carga").hide();
	    	}});
	});
	//-----------------------------------------------------------------------------------------------------
	$('#texto_busqueda').autocomplete({
		source: "control_pagos_cliente_autocomplete.php"
		});
	//----------------------------------------------------------------------------------------------------
	 $("#texto_busqueda").keypress(function(e){
       var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){

		$("#carga").show();

         abuscar = $("#texto_busqueda").val();
         tipo_venta = $("#tipo_venta").val();
		$("#sucursal").val(0);

		estado=$("#estado").val();

		$.ajax({url:"control_pagos_cliente_buscar.php",cache:false,type:"POST",data:{abuscar:abuscar, tipo_venta:tipo_venta, est:estado},success:function(result){
	      	$("#actualizar").html(result);
	      	$("#carga").hide();
	    	}});
      }
 });

	//-----------------------------------------------------------------------------------------------------

	$("#buscar").click(function(){
		$("#carga").show();

         abuscar = $("#texto_busqueda").val();
         tipo_venta = $("#tipo_venta").val();
		$("#sucursal").val(0);


		estado=$("#estado").val();

		$.ajax({url:"control_pagos_cliente_buscar.php",cache:false,type:"POST",data:{abuscar:abuscar, tipo_venta:tipo_venta, est:estado},success:function(result){
	      	$("#actualizar").html(result);
	      	$("#carga").hide();
	    	}});

	});

});