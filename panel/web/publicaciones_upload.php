<?php
include("../funciones/func_mysql.php");
conectar();
mysql_query("SET NAMES 'utf8'");

   extract($_POST);

        $trozos = explode(".", $_FILES['publicacion']['name']);
		$extension = end($trozos);

        $archivo = time("d-m-Y H:i:s").".".$extension;//.$_FILES['publicacion']['name'];
        $tipo = $_FILES['publicacion']['type'];
        $url = "../publicaciones/".$archivo;




		if (!move_uploaded_file($_FILES['publicacion']['tmp_name'], $url)) {
			$url=$url_dos;
			$url_asesor = $url_dos;
		}else{
			$url_asesor = "../../panel/publicaciones/".$archivo;
		}



        	$SQL="INSERT INTO publicaciones (fecha, idsucursal, idusuario, id_tema, obs, url)VALUES('$fecha','$idsucursal','$idasesor','$id_tema','$obs','$url')";
        	mysqli_query($con, $SQL);

        	// include("publicaciones_lista_cuerpo.php");

        	$rs = mysql_query("SELECT MAX(id_publicacion) AS id FROM publicaciones");
			if ($row = mysql_fetch_row($rs)) {
			$id_publicacion= trim($row[0]);
			}

			$cad='';

			if ($idsucursal!=0) {
				$cad .= " AND idsucursal =".$idsucursal;
			}

			if ($idasesor!=0) {
				$cad .= " AND idusuario =" . $idasesor;
			}


			$SQL="SELECT * FROM usuarios WHERE idperfil = 3 ".$cad;
			$res_usu=mysqli_query($con, $SQL);

			while ($usu=mysqli_fetch_array($res_usu)) {
				$id_usuario = $usu['idusuario'];
				$SQL="INSERT INTO publicaciones_linea (id_publicacion, idusuario, id_tema, url) VALUES ('$id_publicacion', '$id_usuario','$id_tema', '$url_asesor')";
				mysqli_query($con, $SQL);
			}
			// echo $cad;





        	header ("Location: publicaciones_lista.php");

        	// echo "exito ".$fecha;



 ?>