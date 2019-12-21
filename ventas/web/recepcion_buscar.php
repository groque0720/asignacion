<?php
//recibo el dato que deseo buscar sugerencias
$campo = $_POST["campo"];
$dato= $_POST["buscar_c"];
$idusuario = $_POST["idusuario"];

if (empty($dato)) {
	$_SESSION["id"] = $idusuario;
	include('recepcion_cuerpo.php');
} else {

//conecto con una base de datos
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

$SQL="SELECT * FROM regrecepcion WHERE idusuario = $idusuario AND $campo LIKE '%$dato%' ORDER BY idcontacto DESC LIMIT 100";
$contacto=mysqli_query($con, $SQL);
// include('recepcion_cuerpo.php');
//  mysqli_close($con);
?>

<table rules="all" border="1" id="mitabla">
	<thead>
		<tr>
			<td width="6%">Fecha</td>
			<td width="7%">Sector</td>
			<td width="12%">Cliente</td>
			<td width="4%">Medio Cont.</td>
			<td width="10%">Teléfono</td>
			<td width="10%">Email</td>
			<td width="8%">Enviado a</td>
<!-- 			<td width="3%">Seg.</td>
			<td width="3%">Siac</td> -->
			<td width="3%">Ver</td>

		</tr>

	</thead>

	<tbody id="lineas_tabla">

	<?php
	$nrofila = 0;
	 while ($cont=mysqli_fetch_array($contacto)) {
	 $nrofila = $nrofila + 1; ?>

	<tr>
		<td style="text-align:center;"><?php echo cambiarformatofecha($cont['fecha']); ?></td>
		<td style="text-align:center;"><?php echo $cont['sector']; ?></td>
		<td><?php echo $cont['cliente']; ?></td>
		<td style="text-align:center;"><?php echo $cont['acercamiento']; ?></td>
		<td style="text-align:center;"><?php echo $cont['telefono']; ?></td>
		<td><?php echo $cont['email']; ?></td>
		<td><?php echo $cont['asesor']; ?></td>
<!-- 		<td style="text-align:center;"><input type="checkbox" value="1" <?php if ($cont['seguimiento']=="1") { echo "checked ";} ?> disabled></td>
		<td style="text-align:center;"><input type="checkbox" value="1" <?php if ($cont['siac']=="1") { echo "checked ";} ?> disabled></td>
 -->		<td style="text-align:center;"><a class="admin" id="<?php echo $cont['idcontacto'];?>" data-id="<?php echo $nrofila; ?>" href=""><img class="lapiz" src="../imagenes/editar.png" title="Ver Registro" width="15px"></a><img class="img_carga" id="<?php echo 'img_'.$nrofila; ?>" src='../imagenes/carga.gif' alt='cargando' width='15px'></td>
	</tr>
	<?php }

	mysqli_close($con);

	 }// cierre contrario si el campo es nulo?>



	</tbody>
</table>

<script>

	function actualizar_paginas(pagina){
		idusuario=$("#idusu").val();
		$.ajax({url:"recepcion_cuerpo.php",cache:false,type:"GET",data:{pagina:pagina, idusuario:idusuario},success:function(result){
	    $("#recepcion_cuerpo").html(result);
	    }});
	};

	$(".img_carga").hide();

	$(".admin").click(function(event){
		event.preventDefault();
		$("#idaccion").val(2);
		id_cont = $(this).attr('id');
		id_linea = $(this).attr('data-id');
		$("#img_"+id_linea).show();
		$("#idcontacto").val(id_cont);
		$("#idlinea").val(id_linea);

		// $($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[9].innerHTML = "<img src='../imagenes/carga.gif' alt='cargando' width='15px'>";

		// alert(id_cont);

		$("#sector").val('');
		$("#cliente").val('');
		$("#med_cont").val('');
		//interes=$("#idusuario").val();
		$("#telefono").val('');
		$("#asignado").val('');
		$("#email").val('');
		$("#seg_siac").val('');
		$("#obs").val('');

		$.ajax({url:"recepcion_carga_ver.php",cache:false,type:"POST",data:{id_cont:id_cont, id_linea:id_linea},success:function(result){
	    $("#parte_act").html(result);
	    }});

	    // $($('#mitabla').find('tbody > tr')[id_linea-1]).children('td')[9].innerHTML = "<a class='admin' id='"+id_cont+"' data-id='"+id_linea+"' href=''><img src='../imagenes/editar.png' title='Ver Registro' width='15px'></a>";

		$("#ventana").dialog("open");
	});

	$(".indice").click(function(event){ // tambien Recepcion_Cuerpo
		event.preventDefault();
		pagina=$(this).attr("data-id");
		actualizar_paginas(pagina);
	})

	$(".flecha").click(function(event){ // tambien Recepcion_Cuerpo
		event.preventDefault();
		pagina=$(this).attr("data-id");
		actualizar_paginas(pagina);
	})

</script>