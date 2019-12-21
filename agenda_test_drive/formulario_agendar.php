<?php 
include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
@session_start();
$id_perfil = $_SESSION["idperfil"];
$id_u_sesion =$_SESSION["id"];
extract($_POST);

$SQL="SELECT * FROM agenda_td_lineas WHERE id_linea = ".$id_linea;
$lineas = mysqli_query($con, $SQL);
$linea=mysqli_fetch_array($lineas);

$SQL="SELECT * FROM usuarios WHERE idperfil = 3";
$usuarios=mysqli_query($con, $SQL);
$usuario_a[1]['nombre']= '-';
while ($usuario=mysqli_fetch_array($usuarios)) {
	$usuario_a[$usuario['idusuario']]['nombre']= $usuario['nombre'];
}

$SQL="SELECT * FROM agenda_td_horarios";
$horarios = mysqli_query($con, $SQL);
$i=1;
while ($periodo=mysqli_fetch_array($horarios)) {
	$periodo_a[$i]['periodo']=$periodo['horario'];
	$i++;
}

$modelo_a[1]="COROLLA";
$modelo_a[2]="HILUX";
$modelo_a[3]="ETIOS 5 PTAS";
$modelo_a[4]="ETIOS SEDAN";

$SQL="SELECT * FROM sucursales";
$sucursales = mysqli_query($con, $SQL);

while ($sucursal=mysqli_fetch_array($sucursales)) {
	$sucursal_a[$sucursal['idsucursal']]['sucursal']=$sucursal['sucursal'];
}

 ?>

		<div class="zona-formulario">

			<form class="form-agendar" action="">

			<div class="centrar-texto titulo-sucursales">
				<span class="centrar-texto ">AGENDAR TEST DRIVE </span>
			</div>

			<hr>

			<div class="zona-titulo-agendar">

				<div class="ancho-50 centrar-texto titulo-sucursales">
					<span class="centrar-texto ">Sucursal: <?php echo strtoupper($sucursal_a[$linea['id_sucursal']]['sucursal']); ?> </span>
				</div>

				<div class="ancho-50 centrar-texto titulo-sucursales">
					<span class="centrar-texto ">Modelo: <?php echo $modelo_a[$linea['id_modelo']];?></span>
				</div>

			</div>

			<input type="hidden" name="id_linea" id="id_linea" value="<?php echo $id_linea; ?>">
			<input type="hidden" name="sucursal" id="sucursal" value="<?php echo $linea['id_sucursal']; ?>">
			<input type="hidden" name="modelo" id="modelo" value="<?php echo $linea['id_modelo']; ?>">
			<input type="hidden" name="fecha" id="fecha" value="<?php echo $linea['fecha']; ?>">

			<hr>

			<div class="flex justificar-flex linea-form">
				<span class="ancho-100 centrar-texto">Fecha: <?php echo cambiarFormatoFecha($linea['fecha']).'  -  Horario: '.$periodo_a[$linea['id_horario']]['periodo']; ?></span>
			</div>

			<div class="flex justificar-flex linea-form">
				<span class="ancho-20 centrar-texto">Cliente:</span>
				<input class="ancho-80" type="text" id="cliente" name="cliente" value="<?php echo $linea['cliente'] ?>" autocomplete="off" placeholder="Nombre cliente">
			</div>
			<div class="flex justificar-flex linea-form">
				<span class="ancho-20 centrar-texto">Teléfono:</span>
				<input class="ancho-80" type="text" id="telefono" name="telefono" value="<?php echo $linea['telefono'] ?>" autocomplete="off" placeholder="Teléfono del cliente">
			</div>

	<?php if ($id_perfil==3) { ?>
			<div class="flex justificar-flex linea-form">
				<span class="ancho-20 centrar-texto">Asesor: </span>
				<select class="ancho-80" name="id_asesor" id="id_asesor">
					<?php 
					$SQL="SELECT * FROM usuarios WHERE idperfil=3 AND activo = 1 AND idsucursal=".$linea['id_sucursal'];
					$asesores = mysqli_query($con, $SQL);
					while ($asesor = mysqli_fetch_array($asesores)) {?>
						<option value="<?php echo $asesor['idusuario']; ?>" <?php if ($asesor['idusuario']==$id_u_sesion) {echo 'selected';	} ?>><?php echo $asesor['nombre']; ?></option>
						<?php } ?>
				</select>
			</div>
	<?php }else{ ?>
			<div class="flex justificar-flex linea-form">
				<span class="ancho-20 centrar-texto">Asesor: </span>
				<select class="ancho-80" name="id_asesor" id="id_asesor">
					<?php 
					$SQL="SELECT * FROM usuarios WHERE idperfil=3 AND activo = 1 AND idsucursal=".$linea['id_sucursal'];
					$asesores = mysqli_query($con, $SQL);
					while ($asesor = mysqli_fetch_array($asesores)) {?>
						<option value="<?php echo $asesor['idusuario']; ?>" <?php if ($asesor['idusuario']==$linea['id_asesor']) {echo 'selected';	} ?>><?php echo $asesor['nombre']; ?></option>
						<?php } ?>
				</select>
			</div>
<?php } ?>


			<div class="flex justificar-flex linea-form">
				<span class="ancho-20 centrar-texto">Realizado: </span>
				<select class="ancho-80" name="realizo" id="">
					<option value="0" <?php if ($linea['realizo']==0) { echo 'selected';} ?>>No</option>
					<option value="1" <?php if ($linea['realizo']==1) { echo 'selected';} ?>>Si</option>
				</select>
			</div>
			<div class="flex justificar-flex linea-form">
			<div>
				<input type="submit" id="limpiar" class="limpiar" value="Limpiar">
				
			</div>
			<div>
				<input type="submit" id="cancelar" class="cancelar" value="Cancelar">
				<input type="submit" id="aceptar" class="aceptar" value="Aceptar">
			</div>
			</div>
				
			</form>

		</div>

<script src="js/form_agendar.js"></script>