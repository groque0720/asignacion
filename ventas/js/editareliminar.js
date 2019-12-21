$('.editar_f').click(function(event) {

		var importe = prompt("Ingrese Monto a Modificar");
		if (importe !=null && importe!='')
			{
			var id=$(this).attr('data-id');
			nro=$("#nrores").val();
			$.ajax({url:"reserva_editar_filas.php",cache:false,type:"POST",data:{idfila:id, monto:importe, nrores:nro },success:function(result){
	      		$("#act_ajax").html(result);
	    		}});
			}
 		 event.preventDefault();
		});

		$('.eliminar_f').click(function(event) {
		id = $(this).attr('data-id');
		nro=$("#nrores").val();
		$.ajax({url:"reserva_eliminar_filas.php",cache:false,type:"POST",data:{idfila:id, nrores:nro },success:function(result){
      		$("#act_ajax").html(result);
    		}});
 		 event.preventDefault();
		});