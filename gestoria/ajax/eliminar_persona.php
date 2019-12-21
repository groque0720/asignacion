<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);


$SQL="DELETE FROM registros_gestoria_clientes WHERE id_cliente_gestoria =".$id_cli;
mysqli_query($con, $SQL);

$SQL="DELETE FROM registros_gestoria_clientes_doc WHERE id_cliente_gestoria =".$id_cli;
mysqli_query($con, $SQL);


$SQL="UPDATE registros_gestoria SET cant_miembro = ".($cant_miembros-1)." WHERE id_reg_gestoria =".$id_reg;
mysqli_query($con, $SQL);


$SQL="SELECT * FROM registros_gestoria_clientes WHERE id_reg_gestoria = ".$id_reg;
$res_cli=mysqli_query($con, $SQL);
$cant=0;

while ($cli=mysqli_fetch_array($res_cli)) {  $cant++; if ($cant==1) { $id_primer_cliente= $cli['id_cliente_gestoria']; $nom_cli=$cli['nombre'];
$estado_primer_cliente = $cli['estado']; $class_cli='cliente-seleccionado cliente input-80';} else{$class_cli='cliente input-80';}?>
	<input type="hidden" id="id_primer_cliente" value="<?php echo $cli['id_cliente_gestoria'] ?>" >
	<input type="hidden" id="nombre_primer_cliente" value="<?php echo $cli['nombre']; ?>" >
	<div class="form-linea">
		<label class="" for=""><?php echo $cant; ?></label>
		<input type="text" id="" data-id="<?php echo $cli['id_cliente_gestoria']; ?>" class="<?php echo $class_cli; ?>" value="<?php echo $cli["nombre"]; ?>" placeholder="Cliente">
		<?php if ($cli['estado']==0) { $class_est_cli="estado-doc icon-asignacion input-5 incompleto";}else{$class_est_cli="estado-doc icon-asignacion input-5 completo";} ?>
		<span data-estado="<?php echo $cli['estado']; ?>" data-id="<?php echo $cli['id_cliente_gestoria']; ?>" class="<?php echo $class_est_cli; ?>"></span>
		<span data-id="<?php echo $cli['id_cliente_gestoria']; ?>" class="icon-borrar input-5 remove_member"></span>
	</div>
<?php  } ?>
<script src="js/js_tramite_ab_clientes.js"></script>