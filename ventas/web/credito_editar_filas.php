<?php

include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="UPDATE creditos_lineas SET observacion='".$_POST['ob']."' WHERE idcreditolinea =".$_POST['idfila'];
mysqli_query($con, $SQL);
?>
<script type="text/javascript">

 	$(document).ready(function(){

 		$('.eliminar_f').click(function(event) {
 		if (confirm("Seguro que deseas borrar la fila??")) {
		id = $(this).attr('data-id');
		nrocred= $("#idcredito").val();

		$.ajax({url:"credito_eliminar_filas.php",cache:false,type:"POST",data:{idfila:id, idcredito: nrocred},success:function(result){
      		$("#act_ajax").html(result);
    		}});
 		 event.preventDefault();
 		};
		});

		$('.editar_f').click(function(event) {
	  		var obs = prompt("Escriba la Observacion a Editar");
				if (obs!="" && obs != null) {
					id = $(this).attr('data-id');
					nrocred= $("#idcredito").val();

					$.ajax({url:"credito_editar_filas.php",cache:false,type:"POST",data:{idfila:id, idcredito: nrocred, ob:obs},success:function(result){
      				$("#act_ajax").html(result);
    				}});

				};
	  	});

 		});

</script>


<?php include("credito_altamodbaja.php");
 mysqli_close($con);
  ?>