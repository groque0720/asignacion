<?php
include("../funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");

$SQL="INSERT INTO creditos_lineas(idcredito, fecha, estado, observacion) VALUES
 (".$_POST["idcredito"].", '".$_POST["fecha"]."', ".$_POST["estado"].",'".$_POST["observacion"]."')";
mysqli_query($con, $SQL);

$SQL="UPDATE creditos SET estado ='".$_POST["estado"]."' WHERE idcredito=".$_POST["idcredito"];
mysqli_query($con, $SQL);

$det_est="";
switch ($_POST["estado"]) {
    case 1:
        $det_est="Recibido";
        break;
    case 2:
        $det_est="Enviado";
        break;
    case 22:
        $det_est="En Análisis";
        break;
    case 3:
        $det_est="Observado";
        break;
    case 4:
        $det_est="Rechazado";
        break;
    case 5:
        $det_est="Pre-Aprobado";
        break;
    case 66:
        $det_est="Aprobado observado";
        break;
    case 6:
        $det_est="Aprobado";
        break;
    case 70:
        $det_est="Liquidado";
        break;
};


$SQL="SELECT * FROM usuarios WHERE idusuario = ". $_POST["idusu"];
$usuarios=mysqli_query($con, $SQL);
if (empty($usuarios)) {$usuario['email']="";}else{ $usuario=mysqli_fetch_array($usuarios);}

// Carga de las notificaciones

$SQL="SELECT * FROM reservas WHERE idreserva =".$_POST["idres"];
$reg_res=mysqli_query($con, $SQL);
$reserva=mysqli_fetch_array($reg_res);

$SQL="SELECT * FROM grupos WHERE idgrupo=".$reserva["idgrupo"];
$gru=mysqli_query($con, $SQL);
if (empty($gru)) {$grupo['grupo']="";}else{ $grupo=mysqli_fetch_array($gru);}

$SQL="SELECT * FROM modelos WHERE idmodelo=".$reserva["idmodelo"];
$mod=mysqli_query($con, $SQL);
if (empty($mod)) {$modelo['modelo']="";}else{ $modelo=mysqli_fetch_array($mod);}

$hora=date( 'H:i');

$SQL="SELECT * FROM notificacionespara WHERE tiponot=5";
$res=mysqli_query($con, $SQL);

while ($not=mysqli_fetch_array($res)) {
$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, idcredito, interno, modelo, cliente, asesor, visto, obs )";
$SQL .=" VALUES (5,'".date("Y-m-d")."','$hora','".$not["idusuario"]."','".$reserva["compra"]."','".$_POST["idres"]."','".$_POST["idcredito"]."','".$reserva["interno"].$reserva["internou"]."','".$grupo['grupo']." ".$modelo['modelo']."".$reserva["detalleu"]."','".$_POST["cliente"]."','".$_POST["asesor"]."',0,'".$det_est.": ".$_POST["observacion"]."')";
mysqli_query($con, $SQL);
}

$SQL="INSERT INTO notificaciones(tiponot, fechanot, hora, idusuario, compra, idreserva, idcredito, interno, modelo, cliente, asesor, visto, obs )";
$SQL .=" VALUES (5,'".date("Y-m-d")."','$hora','".$_POST["idusu"]."','".$reserva["compra"]."','".$_POST["idres"]."','".$_POST["idcredito"]."','".$reserva["interno"].$reserva["internou"]."','".$grupo['grupo']." ".$modelo['modelo']."".$reserva["detalleu"]."','".$_POST["cliente"]."','".$_POST["asesor"]."',0,'".$det_est.": ".$_POST["observacion"]."')";
mysqli_query($con, $SQL);
// Carga de las notificaciones

// $email = "online@dyv-online.com.ar";

// $titulo="NOTIFICACION ESTADO DE CREDITO - CLIENTE: ".$_POST["cliente"];

// $mensaje = "SE NOTIFICA QUE EL CREDITO DEL CLIENTE: '".$_POST["cliente"]."'\n OPERACION DE: '".$_POST["asesor"]."'\n";
// $mensaje .= "ESTA EN EL ESTADO DE '".$_POST["opcion"]."'\n";
// $mensaje .= "OBSERVACION: ".$_POST["observacion"]."\n";
// $mensaje .= "LINK: http://dyvsa.com.ar/web/credito.php?IDrecord=".$_POST["idcredito"];


// $parados = "rukyguerra@derkayvargas.com.ar, vargasofredy@derkayvargas.com.ar, lauraderka@derkayvargas.com.ar,".$usuario["email"];


// $headers = 'From: online@dyvsa.com.ar' . "\r\n" .
//     		'Reply-To: online@dyv-online.com.ar' . "\r\n" .
//     		'X-Mailer: PHP/' . phpversion();

//   if (($_POST["cliente"]) != null and $_POST["idcredito"]!= null) {
//     	mail($parados, $titulo, $mensaje, $headers);
//     		}

if ($det_est=="Liquidado") {

		$SQL="INSERT INTO pagos_lineas(idreserva, fecha, tipo, modo, financiera, nrorecibo, monto, obs) VALUES
		(".$_POST["idres"].", '".$_POST["fecha"]."', 2, 3, ".$_POST["fin"].",'-',".$_POST["monto_p"].",'".$_POST["observacion"]."')";
		mysqli_query($con, $SQL);
	}
?>

<table rules="all" border="1" style="width: 100%;">
	<thead>
		<tr>
			<td width="10%">Fecha</td>
			<td width="10%">Estado</td>
			<td width="68%">Observaci&oacute;n</td>
			<td width="7%">Editar</td>
		</tr>
	</thead>

	<tbody>

			<?php
			$SQL="SELECT * FROM creditos_lineas WHERE idcredito =".$_POST["idcredito"];
			$lineas_creditos= mysqli_query($con, $SQL);

			while ($lineas=mysqli_fetch_array($lineas_creditos)) { ?>
			<tr>
			<td><?php echo cambiarformatofecha($lineas["fecha"]); ?> </td>
			<td>
				<select id="estado_l" name="estado_l" disabled>
					<option value="0"></option>
					<option value="1" <?php if ($lineas['estado']==1) {  echo "selected";} ?>>Recibido</option>
					<option value="2" <?php if ($lineas['estado']==2) {  echo "selected";} ?>>Enviado</option>
					<option value="22" <?php if ($lineas['estado']==22) {  echo "selected";} ?>>En An&aacute;lisis</option>
					<option value="3" <?php if ($lineas['estado']==3) {  echo "selected";} ?>>Observado</option>
					<option value="4" <?php if ($lineas['estado']==4) {  echo "selected";} ?>>Rechazado</option>
					<option value="5" <?php if ($lineas['estado']==5) {  echo "selected";} ?>>Pre-Aprobado</option>
					<option value="6" <?php if ($lineas['estado']==6) {  echo "selected";} ?>>Aprobado</option>
					<option value="66" <?php if ($lineas['estado']==66) {  echo "selected";} ?>>Aprobado Obs</option>
					<option value="70" <?php if ($lineas['estado']==70 || $lineas['estado']==7) {  echo "selected";} ?>>Liquidado</option>
				</select>
			</td>
			<td><?php echo $lineas['observacion'] ?> </td>
			<td>
				<a class="editar_f" href="#" data-id="<?php echo $lineas["idcreditolinea"];?>"><img src="../imagenes/editar.png"  width="20px"></a>
				<a class="eliminar_f" href="#" data-id="<?php echo $lineas["idcreditolinea"];?>"><img src="../imagenes/eliminar.png"  width="20px"></a>
			</td>

			</tr>
			<?php } ?>



	</tbody>
<?php  mysqli_close($con);  ?>
</table>


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