  <?php 
	extract($_POST);

	$cadena='';

	if ($id_asesor!=0) {
		$cadena.= ' AND id_asesor='.$id_asesor;
	}

	if ($id_grupo!=0) {
		$cadena.= ' AND id_grupo='.$id_grupo;
	}

	if ($id_modelo!=0) {
		$cadena.= ' AND id_modelo='.$id_modelo;
	}

	if ($filtro_mes==1) {
		
		if ($mes_desde!=0) {
			$cadena.=' AND id_mes ='.$mes_desde;
		}

		if ($año_desde!='' AND $año_desde>=2016) {
			$cadena.=' AND año ='.$año_desde;
		}
	}

	if ($filtro_mes==2) {
		if ($mes_desde!=0) {
			$cadena.=' AND id_mes >='.$mes_desde;
		}
		if ($año_hasta!='' AND $año_hasta>=2016) {
			$cadena.=' AND año >='.$año_hasta;
		}
	}

	if ($filtro_mes==3) {
		if ($mes_desde!=0) {
			$cadena.=' AND id_mes <='.$mes_desde;
		}
		if ($año_hasta!='' AND $año_hasta>=2016) {
			$cadena.=' AND año <='.$año_hasta;
		}
	}

	if ($filtro_mes==4) {

		if ($mes_desde!=0) {
			$cadena.=' AND ((id_mes >='.$mes_desde;
		}
		if ($año_desde!='' AND $año_desde>=2016) {
			$cadena.=' AND año >='.$año_desde.')';
		}
		if ($mes_hasta!=0) {
			$cadena.=' OR (id_mes <='.$mes_hasta;
		}
		if ($año_hasta!='' AND $año_hasta>=2016) {
			$cadena.=' AND año <='.$año_hasta.'))';
		}
	}

	if ($cancelada!=0) {
		$cadena .= ' AND cancelada ='.$cancelada;
	}


//echo $cadena;

//$SQL="SELECT * FROM asignaciones WHERE entregada = 0 AND id_modelo = ". $modelo['idmodelo'] ." ORDER BY año, id_mes, nro_orden, nro_unidad";
?>



<iframe src="<?php echo 'filtro_avanzado_respuesta.php?cadena='.$cadena; ?>"
 width="100%" height="500" style="border: none;">
 </iframe>
