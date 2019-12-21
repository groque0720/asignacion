<?php 
	include("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$dato_comprobar;
	$item_comprobar;
	$id_unidad;

	if ($item=='nro_orden') {

		$SQL="SELECT id_unidad FROM asignaciones WHERE nro_orden = $dato_comprobar AND id_unidad <> $id_unidad";
		$res=mysqli_query($con, $SQL);
		$cantidad = mysqli_num_rows($res);

		if ($cantidad>0) {
			echo '<script>
						$("#usuario").facus();
						swal("VALOR DUPLICADO", "Por favor verifique que los datos de Nro Orden sean correctos", "error");
					</script>'
		}

	}

 ?>

