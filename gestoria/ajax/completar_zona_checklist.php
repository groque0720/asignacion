<?php 

include("../funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");

extract($_POST);

	$SQL="SELECT * FROM registros_gestoria_clientes_doc WHERE id_cliente_gestoria =". $id_cli;
	$res_doc = mysqli_query($con, $SQL);

?>


<h2 class="form_titulo">CHECK LIST U.I.F.</h2>
<h2 class="form_titulo"><?php echo $cli; ?></h2>
<div class="zona-checks">
	<div class="input-100">
		<?php 
			$check=0;
			while ($doc=mysqli_fetch_array($res_doc)) { $check++;?>
			
				<div class="form-linea linea-doc">
					

					<?php
					 $SQL="SELECT * FROM registros_gestoria_uif_doc WHERE id_doc_uif = ".$doc['id_doc_uif'];
					 $res_uif = mysqli_query($con, $SQL);
					 $uif=mysqli_fetch_array($res_uif);
					 ?>

					<label class="input-85" for="<?php echo 'check_'.$check; ?>"><?php echo $uif['documentacion'];?></label>
					<input class="item_chech input-10" data-id="<?php echo $doc['id_doc_cli']; ?>" type="checkbox" id="<?php echo 'check_'.$check; ?>" name="<?php echo 'check_'.$check; ?>" <?php if ($doc['estado']==1) {
						echo 'checked';
					} ?>>

				</div>
		<?php }	 ?>
	</div>
</div>

<script>

$('.item_chech').click(function(event) {
	id_doc=$(this).attr('data-id');
	if ($(this).prop('checked')) {
		valor=1;
	}else{
		valor=0;
	};

	$.ajax({
		url:"ajax/validar_doc.php",
		cache:true,
		type:"POST",
		data:{id_doc:id_doc, valor:valor},
		success:function(result){
		}
	});

});
</script>