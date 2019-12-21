	$(".item_link").click(function(event) {
		event.preventDefault();

		if ($(this).attr('data-id')==1) {
			$(".mod").show();
			valor="";
			$.ajax({
				url:'agenda_cuerpo.php',
				cache:false,
				type:"POST",
				data:{valor},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-ppal").html(result);
	    		}
	    	});
		}

		if ($(this).attr('data-id')==2) {
			$(".mod").show();
			nuevaUnidad='nuevaUnidad';
			$.ajax({
				url:"informe.php",
				cache:false,
				type:"POST",
				data:{nuevaUnidad:nuevaUnidad},
				success:function(result){
					$(".mod").hide();
	     			$(".contenido-ppal").html(result);
	    		}
	    	});
		}

});

