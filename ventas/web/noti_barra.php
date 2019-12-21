	<?php

		include_once("../funciones/func_mysql.php");
		include_once("../includes/security.php");
		conectar();
		//mysql_query("SET NAMES 'utf8'");

	 ?>

		<script src="../js/ion.sound.js"></script>
	  	<script src="../js/noti_barra.js" defer></script>


<div class="logo_dyv">

	<div class="box_imagen_logo" id="img_res">
		<img src="../imagenes/dyv_b.ico">
		<div class="nombre_emp">
			DERKA Y VARGAS S. A.
		</div>
	</div>

</div>



<div class="estado" id="estado">

</div>


<?php

//$SQL="DELETE FROM notificaciones_view";
//mysqli_query($con, $SQL);

$usu=$_SESSION["id"];



 ?>




<div class="noti_iconos" id="noti_iconos">

	<input type="hidden" id="id_usuario_dyv" value="<?php echo $usu ?>">

	<div class="noti" id="noti_res">
		<div class="cantidad" id="cant_res">

			<?php

				$SQL="SELECT count(tiponot) as cantidad FROM notificaciones WHERE tiponot = 1 AND idusuario =$usu AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
				if ($cant_res['cantidad']>0) {echo $cant_res['cantidad'];}
			 ?>

		</div>

		<div class="box_imagen" id="img_res" <?php if ($cant_res['cantidad']==0) { echo "style= 'opacity: .6'";} ?>>
			<img src="../imagenes/reserva_mas.png">
		</div>

	</div>

	<div class="noti" id="noti_act">
		<div class="cantidad" id="cant_act">
			<?php
				$SQL="SELECT count(tiponot) as cantidad FROM notificaciones WHERE tiponot = 2 and idusuario =$usu AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
				if ($cant_res['cantidad']>0) {echo $cant_res['cantidad'];}
			 ?>
		</div>

		<div class="box_imagen" id="img_res" <?php if ($cant_res['cantidad']==0) { echo "style= 'opacity: .6'";} ?>>
			<img src="../imagenes/reserva_act.png">
		</div>

	</div>


<div class="noti" id="noti_anu">
		<div class="cantidad" id="cant_anu">
			<?php
				$SQL="SELECT count(tiponot) as cantidad FROM notificaciones  WHERE tiponot = 3 AND idusuario =$usu AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
				if ($cant_res['cantidad']>0) {echo $cant_res['cantidad'];}

			 ?>
		</div>

		<div class="box_imagen" id="img_res" <?php if ($cant_res['cantidad']==0) { echo "style= 'opacity: .6'";} ?>>
			<img src="../imagenes/eliminar.png">
		</div>

	</div>

	<div class="noti" id="noti_fact">
		<div class="cantidad" id="cant_fact">
			<?php
				$SQL="SELECT count(tiponot) as cantidad FROM notificaciones WHERE tiponot = 4 AND idusuario =$usu AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
				if ($cant_res['cantidad']>0) {echo $cant_res['cantidad'];}

			 ?>
		</div>

		<div class="box_imagen" id="img_res" <?php if ($cant_res['cantidad']==0) { echo "style= 'opacity: .6'";} ?>>
			<img src="../imagenes/facturacion_mas.png">
		</div>

	</div>

	<div class="noti" id="noti_cred">
		<div class="cantidad" id="cant_cred">
			<?php
				$SQL="SELECT count(tiponot) as cantidad FROM notificaciones WHERE tiponot = 5 AND idusuario =$usu AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
				if ($cant_res['cantidad']>0) {echo $cant_res['cantidad'];}
			 ?>
		</div>

		<div class="box_imagen" id="img_res" <?php if ($cant_res['cantidad']==0) { echo "style= 'opacity: .6'";} ?>>
			<img src="../imagenes/credito.png">
		</div>

	</div>

	<div class="noti" id="noti_canc">
		<div class="cantidad" id="cant_canc">
			<?php
				$SQL="SELECT count(tiponot) as cantidad FROM notificaciones WHERE tiponot = 6 AND idusuario =$usu AND visto=0 and borrar=0";
				$res=mysqli_query($con, $SQL);
				if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}
				if ($cant_res['cantidad']>0) {echo $cant_res['cantidad'];}
			 ?>
		</div>

		<div class="box_imagen" id="img_res" <?php if ($cant_res['cantidad']==0) { echo "style= 'opacity: .6'";} ?>>
			<img src="../imagenes/pagos_ok.png">
		</div>

	</div>


<!-- Compruebo que las cantidades son iguales -->

<?php
	$SQL="SELECT count(tiponot) as cantidad FROM notificaciones WHERE idusuario =$usu AND visto=0 and borrar=0";
	$res=mysqli_query($con, $SQL);
	if (empty($res)) {$cant_res['cantidad']=0;}else{ $cant_res=mysqli_fetch_array($res);}

 ?>

<div class="cant_actualizado" id="cant_actualizado" style="widht:30px;">
<?php echo $cant_res['cantidad']; ?>
</div>

<input type="hidden" id="numero_act" value="<?php echo $cant_res['cantidad']; ?>">


</div>



