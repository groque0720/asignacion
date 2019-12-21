$('.editar_f').click(function(event) {
		var importe = prompt("Ingrese Monto a Modificar");
		if (importe !=null && importe!='')
			{
			var id=$(this).attr('data-id');
			nro=$("#nrores").val();
			nrofact=$("#idfact").val();
			$.ajax({url:"facturacion_editar_filas.php",cache:false,type:"POST",data:{idfila:id, monto:importe, idfact:nrofact, nrores:nro },success:function(result){
	      		$("#act_ajax").html(result);
	    		}});
			}
 		 event.preventDefault();
		});
		

		$('.eliminar_f').click(function(event) {
		id = $(this).attr('data-id');
		nro=$("#nrores").val();
		nrofact=$("#idfact").val();
		$.ajax({url:"facturacion_eliminar_filas.php",cache:false,type:"POST",data:{idfila:id, idfact:nrofact, nrores:nro },success:function(result){
      		$("#act_ajax").html(result);
    		}});
 		 event.preventDefault();
		});