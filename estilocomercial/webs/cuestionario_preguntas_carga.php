<?php
set_time_limit(300);
	include("../_seguridad/_seguridad.php");
	include("../funciones/func_mysql.php");
	conectar();
	mysql_query("SET NAMES 'utf8'");
	$id_cuestionario=$_GET["id"];
	$encuesta=$_GET["cue"];


	if (isset($_GET["op"])) {
		$SQL="UPDATE cuestionarios SET id_encuesta =".$encuesta." WHERE id_cuestionario=".$id_cuestionario;
		mysqli_query($con, $SQL);
		$SQL="DELETE FROM cuestionarios_respuestas WHERE id_cuestionario=".$id_cuestionario;
		mysqli_query($con, $SQL);
		$SQL="DELETE FROM cuestionarios_respuestas_lineas WHERE id_cuestionario=".$id_cuestionario;
		mysqli_query($con, $SQL);
	}

	$SQL="SELECT nro_pregunta FROM cuestionarios_respuestas WHERE id_cuestionario=".$id_cuestionario;
	$resu=mysqli_query($con, $SQL);
	$cant=mysql_num_rows($resu);

	if ($cant==0) { //compruebo que no haya ya preguntas cargadas

		$SQL="SELECT * FROM encuestas_preguntas WHERE id_encuesta =".$encuesta."   AND baja = 0 ORDER BY nro_pregunta";
	    $res=mysqli_query($con, $SQL);

		while ($preguntas=mysqli_fetch_array($res)) {

			$SQL="INSERT INTO cuestionarios_respuestas";
			$SQL .="(nro_pregunta ,";
			$SQL .="id_formato_respuesta ,";
			$SQL .="id_encuesta ,";
			$SQL .="id_cuestionario ,";
			$SQL .="si_respuesta ,";
			$SQL .="proxima_pregunta ,";
			$SQL .="id_pregunta) VALUES ";
			$SQL .="(".$preguntas["nro_pregunta"].",";
			$SQL .="".$preguntas["id_formato_respuesta"].",";
			$SQL .="".$encuesta.",";
			$SQL .="".$id_cuestionario.",";
			$SQL .="".$preguntas["si_respuesta"].", ";
			$SQL .="".$preguntas["proxima_pregunta"].", ";
			$SQL .="".$preguntas["id_pregunta"].")";
			mysqli_query($con, $SQL);

			$rs = mysql_query("SELECT MAX(id_respuesta_cuestionario) AS id FROM cuestionarios_respuestas");
			if ($row = mysql_fetch_row($rs)) {$id_ultimo=trim($row[0]);}

			$id_tipo_respuesta = $preguntas["id_tipo_respuesta"];


			$SQL="SELECT * FROM encuestas_lineas_respuestas WHERE id_tipo_respuesta =".$id_tipo_respuesta;
			$res_linea=mysqli_query($con, $SQL);

			while ($lineas=mysqli_fetch_array($res_linea)) {
				$SQL="INSERT INTO cuestionarios_respuestas_lineas (";
				$SQL .=" id_cuestionario, id_respuesta_cuestionario,id_linea_tipo_respuesta, linea_tipo_respuesta) VALUES(".$id_cuestionario.",".$id_ultimo.",".$lineas["id_linea_tipo_respuesta"].",'".$lineas["linea_tipo_respuesta"]."')";
				mysqli_query($con, $SQL);
			}
		}
	}
?>

<?php if (!empty($_GET["op"])): ?>
<?php include("cuestionario_cuerpo_preguntas.php"); ?>
<script>

		$(".celda_1").addClass("pregunta_activa");



		//------------------------------------------------------------
			function click_pregunta(activa){

				cant=$("#cant_preg").val();

				for (var i = 1; i <= cant; i++) {
					$(".celda_"+i).removeClass("pregunta_activa");
				};

				$(".celda_"+activa).addClass("pregunta_activa");

			}
//--------------------------------------------------------------

			function click_proxima(prox){

				cant=$("#cant_preg").val();

				for (var i = 1; i <= cant; i++) {
					$(".celda_"+i).removeClass("pregunta_proxima");
				};

				$(".celda_"+prox).addClass("pregunta_proxima");


			}
	//------------------------------------------------------------------------
		$(".pipa").change(function(){
			// alert($(this).attr("name")+"-"+$(this).attr("data-nro")+"-"+$(this).val());
			// alert($(this).attr("data-si"));
			var string=$(this).attr("data-si")
			var item= string.split("-");

			var prox = parseInt($(this).attr("data-preg"))+1;


				if (item[0]==item[2]|| $(this).attr("name")==2) {
					$(".celda_"+item[1]).addClass("pregunta_proxima");
					click_proxima(item[1]);
				}else{
					click_proxima(prox);
				};

			click_pregunta($(this).attr("data-preg"));
			formato=$(this).attr("name");
			id_resp=$(this).val();
			nro_pregunta=$(this).attr("data-nro");
			id_cuestionario = $("#id_cuestionario").val();
			id_estado=$("#id_estado_cuestionario").val();
			$.post("cuestionario_preguntas_procesar.php",
			{formato:formato,id:id_resp, nro:nro_pregunta, id_cuestionario:id_cuestionario, id_estado:id_estado},
			function(result){$("#cambio_estado").html(result);});
		})

		$(".pipa_ta").focus(function(){

			click_pregunta($(this).attr("data-preg"));

			var prox = parseInt($(this).attr("data-preg"))+1;

			if ($(this).attr("data-form")==3) {
				click_proxima(prox);
			}
			// else{
			// var string=$(this).attr("data-si")
			// var item= string.split("-");
			// click_proxima(item[1]);
			// };
		})

		$(".pipa_ta").change(function(){
			// alert($(this).attr("name")+"-"+$(this).attr("data-nro")+"-"+$(this).val());
			formato=$(this).attr("name");
			valor=$(this).val();
			nro_pregunta=$(this).attr("data-nro");
			id_cuestionario = $("#id_cuestionario").val();
			id_estado=$("#id_estado_cuestionario").val();
			$.post("cuestionario_preguntas_procesar.php",
			{formato:formato, nro:nro_pregunta, texto:valor, id_cuestionario:id_cuestionario, id_estado:id_estado});
		})
</script>
<?php endif ?>
