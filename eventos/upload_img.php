<?php

include("funciones/func_mysql.php");
conectar();
//mysql_query("SET NAMES 'utf8'");


//--------------------------------------------------------------------------------
function redim($ruta1,$ruta2,$ancho,$alto)
    {
    # se obtene la dimension y tipo de imagen
    $datos=getimagesize ($ruta1);

    $ancho_orig = $datos[0]; # Anchura de la imagen original
    $alto_orig = $datos[1];    # Altura de la imagen original
    $tipo = $datos[2];


    if ($tipo==1){ # GIF
        if (function_exists("imagecreatefromgif"))
            $img = imagecreatefromgif($ruta1);
        else
            return false;
    }
    else if ($tipo==2){ # JPG
        if (function_exists("imagecreatefromjpeg"))
            $img = imagecreatefromjpeg($ruta1);
        else
            return false;
    }
    else if ($tipo==3){ # PNG
        if (function_exists("imagecreatefrompng"))
            $img = imagecreatefrompng($ruta1);
        else
            return false;
    }

    # Se calculan las nuevas dimensiones de la imagen
    if ($ancho_orig>$alto_orig)
        {
        $ancho_dest=$ancho;
        $alto_dest=($ancho_dest/$ancho_orig)*$alto_orig;
        }
    else
        {
        $alto_dest=$alto;
        $ancho_dest=($alto_dest/$alto_orig)*$ancho_orig;
        }

    // imagecreatetruecolor, solo estan en G.D. 2.0.1 con PHP 4.0.6+
    $img2=@imagecreatetruecolor($ancho_dest,$alto_dest) or $img2=imagecreate($ancho_dest,$alto_dest);

    // Redimensionar
    // imagecopyresampled, solo estan en G.D. 2.0.1 con PHP 4.0.6+
    @imagecopyresampled($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig) or imagecopyresized($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig);

    // Crear fichero nuevo, según extensión.
    if ($tipo==1) // GIF
        if (function_exists("imagegif"))
            imagegif($img2, $ruta2);
        else
            return false;

    if ($tipo==2) // JPG
        if (function_exists("imagejpeg"))
            imagejpeg($img2, $ruta2);
        else
            return false;

    if ($tipo==3)  // PNG
        if (function_exists("imagepng"))
            imagepng($img2, $ruta2);
        else
            return false;

    return true;
    }
//-----------------------------------------------------------------------------------------
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

$uploads_dir = 'imagenes/';
$id_evento=$_POST["id_evento"];

if ($_FILES['images']) {
    $file_ary = reArrayFiles($_FILES['images']);

    $leyenda="";

    foreach ($file_ary as $file) {
        // print 'Nombre: ' . $file['name'];
        $tipo=$file['type'];
        $size=$file['size'];

	    if ($tipo != 'image/JPEG' && $tipo != 'image/JPG' && $tipo != 'image/jpg' && $tipo != 'image/jpeg' && $tipo != 'image/png' && $tipo != 'image/PNG' && $tipo != 'image/gif')
	    {
	    	$leyenda .=" El archivo '".$file['name']."' no es un archivo valido o Supera el tamaño <br>";
	    }
	    else
		 if ($size > 5500*5500) //1024
	    {
	            $leyenda .=" El archivo '".$file['name']."' supera el tamaño de 2mb <br>";
	    }else{

            $rs = mysql_query("SELECT MAX(id_img) AS id FROM imagenes");
            if ($row = mysql_fetch_row($rs)) {
                $nro_imagen=trim($row[0])+1;
            }else{
                $nro_imagen=1;
            }

            $SQL="INSERT INTO imagenes (id_evento, url) VALUES (".$id_evento.", '".$uploads_dir.$nro_imagen."_".$file['name']."')";
	    	mysqli_query($con, $SQL);
	    	move_uploaded_file($file['tmp_name'], $uploads_dir.$nro_imagen."_".$file['name']);
                        # ruta de la imagen a redimensionar
            $imagen=$uploads_dir.$nro_imagen."_".$file['name'];
            # ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
            $imagen_final=$uploads_dir.$nro_imagen."_".$file['name'];
            $ancho_nuevo=800;
            $alto_nuevo=600;
            //redim($imagen,$imagen_final,$ancho_nuevo,$alto_nuevo);
	    }
    }


		$SQL="SELECT * FROM imagenes WHERE id_evento=".$id_evento;
		$imagenes=mysqli_query($con, $SQL);
		$nro_imagen=0;
		while ($img=mysqli_fetch_array($imagenes)) {
			$nro_imagen=$nro_imagen+1;?>
			<div class="ed-item web-1-6 div_img" id="<?php echo "img_".$nro_imagen; ?>">
				<a href="" class="imagen_pro" data-url ="<?php echo $img["url"]; ?>" data-nro="<?php echo $nro_imagen;?>" id="<?php echo $img["id_img"] ?>">X</a>
				<img src="<?php echo $img["url"]; ?>" alt="foto_evento">
			</div>
		<?php } ?>

		<div class="ed-item error_carga">
			<?php  echo $leyenda; ?>
		</div>

		 <script>
            $(".imagen_pro").click(function(event){
                event.preventDefault();
                nro_imagen=$(this).attr("data-nro");
                id=$(this).attr("id");
                if (confirm('Estás seguro que deseas eliminar la imagen?')) {
                    $(".carga_gif").show();
                    operacion="Eliminar_imagen";
                    url=$(this).attr("data-url");
                    $.ajax({
                        url:"evento_abm.php",
                        cache:false,
                        type:"POST",
                        data:{operacion:operacion, id_img:id, url:url},
                        success:function(result){
                            $(".carga_gif").hide();
                            $("#img_"+nro_imagen).hide(200);
                        }
                    });
                };
            })
         </script>


<?php }

    // # ruta de la imagen a redimensionar
    // $imagen=$src;
    // # ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
    // $imagen_final=$src;
    // $ancho_nuevo=800;
    // $alto_nuevo=600;

mysqli_close($con);
 ?>
