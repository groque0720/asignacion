$(".cuadro-input").focusin(function(){
	$(".cuadro-input").removeClass('fondo-rojo-1');
	$(this).addClass('fondo-rojo-1');
	this.select();

	// $(".filas").removeClass('fondo-azul-2');
	// $("."+$(this).attr('data-fila')).addClass('fondo-azul-2');
})


$(".cuadro-input").keypress(function(event){
	var keycode = (event.keyCode ? event.keyCode : event.which);

	  if(keycode == '13'){

	  	fila=parseInt($(this).attr('data-nrofila'))+1;
	  	columna = $(this).attr('data-columna');

	  	$("."+fila+'-'+columna).focus();

	  }

});

$(".cuadro-input").focusout(function(){

	id=$(this).attr('data-id');
	valor=$(this).val();
	que_csi=$(this).attr('data-csi');


	$.ajax({
		url:"csi_sucursales_actualizar.php",
		cache:false,
		type:"POST",
		data:{id, valor, que_csi},
		success:function(result){
		}
	});

});	

$(".detalle_csi_suc_tasa").click(function(event){
	event.preventDefault();
	$("#tabla_csi_sucursales").hide();
	$("#tabla_csi_sucursales_detalle").show();
	$(".mod").show();

	id=$(this).attr('data-id');
	mes=$("#mes").val();
	ano=$("#ano").val();
	sucursal=$(this).attr('data-sucursal');

	$.ajax({
		url:"csi_sucursales_cuerpo_detalle_tasa.php",
		cache:false,
		type:"POST",
		data:{id, mes, ano, sucursal},
		success:function(result){
			$("#tabla_csi_sucursales_detalle").html(result);
			$(".mod").hide();
		}
	});
});

$(".detalle_csi_suc_dyv").click(function(event){
	event.preventDefault();
	$("#tabla_csi_sucursales").hide();
	$("#tabla_csi_sucursales_detalle").show();
	$(".mod").show();

	id=$(this).attr('data-id');
	mes=$("#mes").val();
	ano=$("#ano").val();
	sucursal=$(this).attr('data-sucursal');

	$.ajax({
		url:"csi_sucursales_cuerpo_detalle_dyv.php",
		cache:false,
		type:"POST",
		data:{id, mes, ano, sucursal},
		success:function(result){
			$("#tabla_csi_sucursales_detalle").html(result);
			$(".mod").hide();
		}
	});
})