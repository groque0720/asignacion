<?php

include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");


//borro la linea del estado dek credito
$SQL="DELETE  FROM creditos_lineas WHERE idcreditolinea =".$_POST['idfila'];
mysqli_query($con, $SQL);
//busco el ultimo registro cargado
$SQL="SELECT * FROM creditos_lineas WHERE idcredito =".$_POST['idcredito']." ORDER BY idcreditolinea DESC LIMIT 1";
$res=mysqli_query($con, $SQL);
$cred=mysqli_fetch_array($res);

//corroboro si hay registros, sino le asigno al estado 20(nada)
if (mysql_num_rows($res)>0) {
$SQL="UPDATE creditos SET estado=".$cred['estado']." WHERE idcredito =".$_POST['idcredito'];
mysqli_query($con, $SQL);
}else{
$SQL="UPDATE creditos SET estado = 0 WHERE idcredito =".$_POST['idcredito'];
mysqli_query($con, $SQL);
}

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
 mysqli_close($con);  ?>