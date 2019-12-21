<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title></title>
	   <link rel="shortcut icon" type="image/x-icon" href="../dyv.ico" />
	    <link rel="stylesheet" type="text/css" media="screen" href="../css/estilo.css">
    <link rel="stylesheet" type="text/css" media="print" href="../css/estilo_info_p.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

  	<script type="text/javascript">

	  	$(document).ready(function(){

		  	$("#busq").click(function(event) {

		  		$("#zona_actualizar").html('-');

		  		if ($("#id_buscar").val()!=null && $("#id_buscar").val()!='') {
		  			
					det=$("#id_buscar").val();
					$.ajax({url:"cancelar_buscar.php",cache:false,type:"POST",data:{abuscar:det },success:function(result){
						$("#zona_actualizar").html(result);
					}});
				}else{ alert("Ingresar Id de Unidad")};

			});

		  });
  	</script>

</head>
<body>

	<input type="text" id="id_buscar" >
	<input type="button" id="busq" class="busq" value="buscar" style="background:#D8F781; color:#000; padding: 5px; border-radius: 5px; margin-left: 20px;"/>
	<div id="zona_actualizar">
	
	</div>
</body>

</html>