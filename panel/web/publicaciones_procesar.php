<?php

	include("../funciones/func_mysql.php");
	conectar();
	//mysql_query("SET NAMES 'utf8'");

	extract($_POST);

//--BUSCO ASESOR----------------------------------------

	if ($operacion == 'buscar_asesor') {?>

		<div class="ed-item asesor">
			<label for="asesor">Asesor</label>
		</div>

		<div class="ed-item">
			<select name="idasesor" id="idasesor">
				<option value="0">Todos</option>
				<?php
					$SQL="SELECT * FROM usuarios WHERE activo = 1 AND idperfil = 3 AND idsucursal = ".$id_suc;
					$res_suc=mysqli_query($con, $SQL);
					while ($suc=mysqli_fetch_array($res_suc)) { ?>
						<option value="<?php echo $suc['idusuario'] ?>"><?php echo $suc['nombre']; ?></option>
				<?php } ?>
			</select>
		</div>

	<?php }





 ?>