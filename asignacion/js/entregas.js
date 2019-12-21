// $(document).ready(function(){
// 
// 	$(".mod").hide();
	$(".lienzo-unidad").hide();
	$(".carga-masiva").hide();

	$(".celda").click(function(event) {
		id_unidad=$(this).attr('data-id');
		$(".mod").show();

		$.ajax({
			url:"unidad.php",
			cache:false,
			type:"POST",
			data:{id_unidad:id_unidad},
			success:function(result){
				$(".mod").hide();
     			$(".lienzo-unidad").html(result);
      			$(".lienzo-unidad").show();
    		}
    	});
	});

	$(".ordenar-entregas").click(function(event) {
		/* Act on the event */
		orden=$(this).attr('data-id');
		$("#orden_por").val(orden);
		id_suc=$("#id_suc").val();

			$(".mod").show();
			$.ajax({
			url:"entregas_ordenar.php",
			cache:false,
			type:"POST",
			data:{orden:orden, id_suc:id_suc},
			success:function(result){
	 			$(".contenido-principal").html(result);
	 			$(".lienzo-unidad").hide();
	 			$(".mod").hide();
			}
		});

	 });

		

// });